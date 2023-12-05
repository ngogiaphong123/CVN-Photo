<?php

namespace App\Controllers;

use App\Common\Enums\StatusCode;
use App\Common\Message\PhotoCategoryMessage;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Exceptions\HttpException;
use App\Services\PhotoCategoryService;

class PhotoCategoryController {
	public function __construct (private readonly PhotoCategoryService $photoCategoryService, private readonly Request $request, private readonly Response $response) {}

	/**
	 * @throws HttpException
	 */
	public function addPhotoToCategory (): void {
		$this->response->response(StatusCode::CREATED->value, PhotoCategoryMessage::ADD_PHOTO_TO_CATEGORY_SUCCESSFULLY->value, $this->photoCategoryService->addPhotoToCategory($this->request->getBody(), Session::get("userId")));
	}

	/**
	 * @throws HttpException
	 */
	public function removePhotoFromCategory (): void {
		$this->response->response(StatusCode::OK->value, PhotoCategoryMessage::REMOVE_PHOTO_FROM_CATEGORY_SUCCESSFULLY->value, $this->photoCategoryService->removePhotoFromCategory($this->request->getBody(), Session::get("userId")));
	}
}
