<?php

namespace App\Controllers;

use App\Common\Enums\StatusCode;
use App\Core\Request;
use App\Core\Response;
use App\Exceptions\HttpException;
use App\Services\CategoryService;

class CategoryController {
	public function __construct (private readonly CategoryService $categoryService, private readonly Request $request, private readonly Response $response) {}

	/**
	 * @throws HttpException
	 */
	public function createCategory (): void {
		$this->response->response(StatusCode::CREATED->value, "Create category successfully", $this->categoryService->create($this->request->getBody(), $_SESSION['userId']));
	}

	/**
	 * @throws HttpException
	 */
	public function updateCategory (): void {
		$this->response->response(StatusCode::OK->value,
			"Update category successfully",
			$this->categoryService->update($this->request->getParam('categoryId'), $this->request->getBody(), $_SESSION['userId'])
		);
	}

	/**
	 * @throws HttpException
	 */
	public function findCategoryPhotos (): void {
		$this->response->response(StatusCode::OK->value, "Get category photos successfully", $this->categoryService->findCategoryPhotos($this->request->getParam('categoryId'), $_SESSION['userId']));
	}

	public function findUserCategories (): void {
		$this->response->response(StatusCode::OK->value, "Get user categories successfully", $this->categoryService->findUserCategories($_SESSION['userId']));
	}
	/**
	 * @throws HttpException
	 */
	public function deleteCategory (): void {
		$this->response->response(StatusCode::OK->value, "Delete category successfully", $this->categoryService->delete($this->request->getParam('categoryId'), $_SESSION['userId']));
	}
}
