<?php

namespace App\Controllers;

use App\Common\Enums\StatusCode;
use App\Core\Request;
use App\Core\Response;
use App\Exceptions\HttpException;
use App\Services\PhotoService;

class PhotoController {
	public function __construct (private readonly PhotoService $photoService, private readonly Request $request, private readonly Response $response) {}

	/**
	 * @throws HttpException
	 */
	public function uploadPhotos (): void {
		$this->response->response(StatusCode::CREATED->value, "Upload photos successfully", $this->photoService->upload($_FILES['photos'], $_SESSION['userId']));
	}

	/**
	 * @throws HttpException
	 */
	public function deletePhoto (): void {
		$this->response->response(StatusCode::OK->value, "Delete photo successfully", $this->photoService->delete($this->request->getParam('photoId'), $_SESSION['userId']));
	}

	public function findUserPhotos (): void {
		$this->response->response(StatusCode::OK->value, "Get user photos successfully", $this->photoService->findUserPhotos($_SESSION['userId']));
	}

	/**
	 * @throws HttpException
	 */
	public function findUserPhoto (): void {
		$this->response->response(StatusCode::OK->value, "Get photo successfully", $this->photoService->findUserPhoto($this->request->getParam('photoId'), $_SESSION['userId']));
	}

	/**
	 * @throws HttpException
	 */
	public function updatePhoto (): void {
		$this->response->response(StatusCode::OK->value, "Update photo successfully", $this->photoService->update($this->request->getParam('photoId'), $this->request->getBody(), $_SESSION['userId']));
	}
}
