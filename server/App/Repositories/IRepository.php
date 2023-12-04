<?php

namespace App\Repositories;

interface IRepository {
	public function create (array $data);

	public function findOne (string $id);

	public function update (string $id, array $data, array $originalData);
}
