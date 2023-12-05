<?php

namespace App\Controllers;

use App\Common\Enums\StatusCode;
use App\Core\Request;
use App\Core\Response;
use App\Exceptions\HttpException;
use App\Services\PhotoCategoryService;

class PhotoCategoryController {
	public function __construct (private readonly PhotoCategoryService $photoCategoryService, private readonly Request $request, private readonly Response $response) {}

	/**
	 * @throws HttpException
	 */
	public function addPhotoToCategory (): void {
		$this->response->response(StatusCode::CREATED->value, "Create photo category successfully", $this->photoCategoryService->addPhotoToCategory($this->request->getBody(), $_SESSION['userId']));
	}

	/**
	 * @throws HttpException
	 */
	public function removePhotoFromCategory (): void {
		$this->response->response(StatusCode::OK->value, "Delete photo category successfully", $this->photoCategoryService->removePhotoFromCategory($this->request->getBody(), $_SESSION['userId']));
	}
}
