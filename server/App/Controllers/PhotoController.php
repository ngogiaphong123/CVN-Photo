<?php

namespace App\Controllers;

use App\Common\Enums\StatusCode;
use App\Common\Message\PhotoMessage;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Exceptions\HttpException;
use App\Services\PhotoService;

class PhotoController {
	public function __construct (private readonly PhotoService $photoService, private readonly Request $request, private readonly Response $response) {}

	/**
	 * @throws HttpException
	 */
	public function uploadPhotos (): void {
		$this->response->response(StatusCode::CREATED->value, PhotoMessage::UPLOAD_PHOTOS_SUCCESSFULLY->value, $this->photoService->upload($this->request->getFile("photos"), Session::get("userId")));
	}

	/**
	 * @throws HttpException
	 */
	public function deletePhoto (): void {
		$this->response->response(StatusCode::OK->value, PhotoMessage::DELETE_PHOTO_SUCCESSFULLY->value, $this->photoService->delete($this->request->getParam('photoId'), Session::get("userId")));
	}

	public function findUserPhotos (): void {
		$this->response->response(StatusCode::OK->value, PhotoMessage::GET_USER_PHOTOS_SUCCESSFULLY->value, $this->photoService->findUserPhotos(Session::get("userId")));
	}

	/**
	 * @throws HttpException
	 */
	public function findUserPhoto (): void {
		$this->response->response(StatusCode::OK->value, PhotoMessage::GET_PHOTO_SUCCESSFULLY->value, $this->photoService->findUserPhoto($this->request->getParam('photoId'), Session::get("userId")));
	}

	/**
	 * @throws HttpException
	 */
	public function updatePhoto (): void {
		$this->response->response(StatusCode::OK->value, PhotoMessage::UPDATE_PHOTO_SUCCESSFULLY->value, $this->photoService->update($this->request->getParam('photoId'), $this->request->getBody(), Session::get("userId")));
	}
}
