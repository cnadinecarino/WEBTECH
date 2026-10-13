<?php

namespace App\Controllers;

use App\Libraries\AvatarStorage;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class Users extends AccountController
{
    public function index(): string|ResponseInterface
    {
        try {
            return view('users/index', [
                'title' => 'User Accounts',
                'users' => (new UserModel())->orderBy('id', 'DESC')->findAll(),
            ]);
        } catch (Throwable $error) {
            return $this->unavailable($error);
        }
    }

    public function new(): ResponseInterface
    {
        return $this->formPage('users/form', ['username' => '', 'full_name' => '', 'role' => '', 'avatar' => null], null);
    }

    public function edit(string $id): ResponseInterface
    {
        $id = $this->recordId($id);
        try {
            $record = $this->findRecord(new UserModel(), $id);
        } catch (PageNotFoundException $error) {
            throw $error;
        } catch (Throwable $error) {
            return $this->unavailable($error);
        }

        return $this->formPage('users/form', $record, $id);
    }

    public function create(): ResponseInterface
    {
        return $this->saveUser(null);
    }

    public function update(string $id): ResponseInterface
    {
        return $this->saveUser($this->recordId($id));
    }

    private function saveUser(?int $id): ResponseInterface
    {
        $data = $this->postedText(['username', 'full_name', 'role']);
        $record = ['avatar' => null];
        try {
            $model = new UserModel();
            if ($id !== null) {
                $record = $this->findRecord($model, $id);
            }
        } catch (PageNotFoundException $error) {
            throw $error;
        } catch (Throwable $error) {
            return $this->unavailable($error);
        }

        $this->validateData($data, [
            'username' => ['label' => 'Username', 'rules' => 'required|max_length[60]'],
            'full_name' => ['label' => 'Full name', 'rules' => 'required|max_length[150]'],
            'role' => ['label' => 'Role', 'rules' => 'permit_empty|max_length[60]'],
        ]);
        $errors = $this->validator->getErrors();
        try {
            if (! isset($errors['username']) && $model->usernameExists($data['username'], $id)) {
                $errors['username'] = 'This username is already in use. Choose another username.';
            }
        } catch (Throwable $error) {
            log_message('error', 'Username check failed: {message}', ['message' => $error->getMessage()]);
            $errors['general'] = 'The account could not be checked. Please try again.';
        }

        $avatars = new AvatarStorage();
        $file = $id === null ? null : $this->request->getFile('avatar');
        if ($id !== null && ($avatarError = $avatars->validate($file)) !== null) {
            $errors['avatar'] = $avatarError;
        }
        $formRecord = array_merge($record, $data);
        if ($errors !== []) {
            return $this->formPage('users/form', $formRecord, $id, $errors, isset($errors['general']) ? 503 : 422);
        }

        $newAvatar = null;
        if ($file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            try {
                $newAvatar = $avatars->prepare($file);
                $data['avatar'] = $newAvatar;
            } catch (Throwable $error) {
                log_message('error', 'Avatar preparation failed: {message}', ['message' => $error->getMessage()]);

                return $this->formPage('users/form', $formRecord, $id, [
                    'avatar' => 'The picture could not be prepared. No account changes were saved. Please select the image again.',
                ], 422);
            }
        }

        $db = null;
        $oldAvatar = null;
        try {
            $db = db_connect();
            if (! $db->transBegin()) {
                throw new \RuntimeException('Could not start account transaction.');
            }

            if ($id !== null) {
                // Lock the current row so concurrent replacements cannot delete the active avatar.
                $table = $db->escapeIdentifiers($db->prefixTable('users'));
                $lock = $db->DBDriver === 'MySQLi' ? ' FOR UPDATE' : '';
                $current = $db->query('SELECT * FROM ' . $table . ' WHERE id = ?' . $lock, [$id])->getRowArray();
                if ($current === null) {
                    throw PageNotFoundException::forPageNotFound('Account not found.');
                }
                $oldAvatar = $current['avatar'];
            }

            $saved = $id === null ? $model->insert($data) : $model->update($id, $data);
            if ($saved === false || ! $db->transStatus() || ! $db->transCommit()) {
                throw new \RuntimeException('User persistence failed.');
            }
        } catch (Throwable $error) {
            if ($db !== null) {
                $db->transRollback();
                $db->resetTransStatus();
            }
            $avatars->deleteManaged($newAvatar);
            if ($error instanceof PageNotFoundException) {
                throw $error;
            }
            log_message('error', 'User save failed: {message}', ['message' => $error->getMessage()]);
            $errors = ['general' => 'The user could not be saved. Your entries are still here; please try again.'];
            // This also handles a competing insert after the initial uniqueness check.
            try {
                if ($model->usernameExists($data['username'], $id)) {
                    $errors = ['username' => 'This username is already in use. Choose another username.'];
                }
            } catch (Throwable) {
                // Keep the safe, generic database error above.
            }

            return $this->formPage('users/form', $formRecord, $id, $errors, isset($errors['username']) ? 422 : 503);
        }

        if ($newAvatar !== null) {
            $avatars->deleteManaged($oldAvatar);
        }

        return redirect()->to(site_url('users'))->with('success', $id === null ? 'User created successfully.' : 'User updated successfully.');
    }
}
