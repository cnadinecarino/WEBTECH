<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class Customers extends AccountController
{
    public function index(): string|ResponseInterface
    {
        try {
            return view('customers/index', [
                'title' => 'Customer Accounts',
                'customers' => (new CustomerModel())->orderBy('id', 'DESC')->findAll(),
            ]);
        } catch (Throwable $error) {
            return $this->unavailable($error);
        }
    }

    public function new(): ResponseInterface
    {
        return $this->formPage('customers/form', ['full_name' => '', 'email' => '', 'phone' => ''], null);
    }

    public function edit(string $id): ResponseInterface
    {
        $id = $this->recordId($id);
        try {
            $record = $this->findRecord(new CustomerModel(), $id);
        } catch (PageNotFoundException $error) {
            throw $error;
        } catch (Throwable $error) {
            return $this->unavailable($error);
        }

        return $this->formPage('customers/form', $record, $id);
    }

    public function create(): ResponseInterface
    {
        return $this->saveCustomer(null);
    }

    public function update(string $id): ResponseInterface
    {
        return $this->saveCustomer($this->recordId($id));
    }

    private function saveCustomer(?int $id): ResponseInterface
    {
        $data = $this->postedText(['full_name', 'email', 'phone']);
        try {
            $model = new CustomerModel();
            if ($id !== null) {
                $this->findRecord($model, $id);
            }
        } catch (PageNotFoundException $error) {
            throw $error;
        } catch (Throwable $error) {
            return $this->unavailable($error);
        }

        if (! $this->validateData($data, [
            'full_name' => ['label' => 'Full name', 'rules' => 'required|max_length[150]'],
            'email' => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[254]'],
            'phone' => ['label' => 'Phone', 'rules' => 'permit_empty|max_length[40]'],
        ])) {
            return $this->formPage('customers/form', $data, $id, $this->validator->getErrors(), 422);
        }

        try {
            $saved = $id === null ? $model->insert($data) : $model->update($id, $data);
            if ($saved === false) {
                throw new \RuntimeException('Customer persistence failed.');
            }
        } catch (Throwable $error) {
            log_message('error', 'Customer save failed: {message}', ['message' => $error->getMessage()]);

            return $this->formPage('customers/form', $data, $id, [
                'general' => 'The customer could not be saved. Your entries are still here; please try again.',
            ], 503);
        }

        return redirect()->to(site_url('customers'))->with('success', $id === null ? 'Customer created successfully.' : 'Customer updated successfully.');
    }
}
