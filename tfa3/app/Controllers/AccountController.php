<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Model;
use Throwable;

/** Small shared helpers for the two account forms. */
abstract class AccountController extends BaseController
{
    protected function postedText(array $fields): array
    {
        $data = [];
        foreach ($fields as $field) {
            $value = $this->request->getPost($field);
            // Reject arrays and strip Unicode whitespace, including non-breaking spaces.
            $data[$field] = is_string($value)
                ? (preg_replace('/^[\s\p{Z}]+|[\s\p{Z}]+$/u', '', $value) ?? '')
                : '';
        }

        return $data;
    }

    protected function recordId(string $id): int
    {
        $value = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($value === false) {
            throw PageNotFoundException::forPageNotFound('Account not found.');
        }

        return $value;
    }

    protected function findRecord(Model $model, int $id): array
    {
        $record = $model->find($id);
        if ($record === null) {
            throw PageNotFoundException::forPageNotFound('Account not found.');
        }

        return $record;
    }

    protected function formPage(string $view, array $record, ?int $id, array $errors = [], int $status = 200): ResponseInterface
    {
        $kind = str_starts_with($view, 'customers/') ? 'Customer' : 'User';

        return $this->response->setStatusCode($status)->setBody(view($view, [
            'title' => ($id === null ? 'New ' : 'Edit ') . $kind,
            'record' => $record,
            'id' => $id,
            'isEdit' => $id !== null,
            'errors' => $errors,
        ]));
    }

    protected function unavailable(Throwable $error): ResponseInterface
    {
        log_message('error', 'Account data unavailable: {message}', ['message' => $error->getMessage()]);

        return $this->response->setStatusCode(503)->setBody(view('accounts/unavailable', [
            'title' => 'Accounts unavailable',
        ]));
    }
}
