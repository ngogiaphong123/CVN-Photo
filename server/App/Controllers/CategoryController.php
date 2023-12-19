<?php

namespace App\Controllers;

use App\Common\Enums\StatusCode;
use App\Common\Message\CategoryMessage;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Exceptions\HttpException;
use App\Services\CategoryService;

class CategoryController
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly Request $request,
        private readonly Response $response
    ) {}

    /**
     * @throws HttpException
     */
    public function createCategory(): void
    {
        $this->response->response(
            StatusCode::CREATED->value,
            CategoryMessage::CREATE_SUCCESSFULLY->value,
            $this->categoryService->create($this->request->getBody(), Session::get("userId"))
        );
    }

    /**
     * @throws HttpException
     */
    public function updateCategory(): void
    {
        $this->response->response(
            StatusCode::OK->value,
            CategoryMessage::UPDATE_SUCCESSFULLY->value,
            $this->categoryService->update(
                $this->request->getParam('categoryId'),
                $this->request->getBody(),
                Session::get("userId")
            )
        );
    }

    /**
     * @throws HttpException
     */
    public function findCategoryPhotosByPage(): void
    {
        $this->response->response(
            StatusCode::OK->value,
            CategoryMessage::GET_CATEGORY_PHOTO_SUCCESSFULLY->value,
            $this->categoryService->findCategoryPhotosByPage(
                $this->request->getParam('categoryId'),
                Session::get("userId"),
                $this->request->getQueryParam('page'),
                $this->request->getQueryParam('limit')
            )
        );
    }

    /**
     * @throws HttpException
     */
    public function findPhotosNotInCategoryByPage(): void
    {
        $this->response->response(
            StatusCode::OK->value,
            CategoryMessage::GET_CATEGORY_PHOTO_SUCCESSFULLY->value,
            $this->categoryService->findPhotosNotInCategoryByPage(
                $this->request->getParam('categoryId'),
                Session::get("userId"),
                $this->request->getQueryParam('page'),
                $this->request->getQueryParam('limit')
            )
        );
    }

    /**
     * @throws HttpException
     */
    public function findAllPhotosNotInCategory(): void
    {
        $this->response->response(
            StatusCode::OK->value,
            CategoryMessage::GET_CATEGORY_PHOTO_SUCCESSFULLY->value,
            $this->categoryService->findPhotosNotInCategory(
                $this->request->getParam('categoryId'),
                Session::get("userId")
            )
        );
    }

    /**
     * @throws HttpException
     */
    public function findCategoryPhotos(): void
    {
        $this->response->response(
            StatusCode::OK->value,
            CategoryMessage::GET_CATEGORY_PHOTO_SUCCESSFULLY->value,
            $this->categoryService->findCategoryPhotos($this->request->getParam('categoryId'), Session::get("userId"))
        );
    }

    public function findUserCategories(): void
    {
        $this->response->response(
            StatusCode::OK->value,
            CategoryMessage::GET_USER_CATEGORY_SUCCESSFULLY->value,
            $this->categoryService->findUserCategories(Session::get("userId"))
        );
    }

    /**
     * @throws HttpException
     */
    public function deleteCategory(): void
    {
        $this->response->response(
            StatusCode::OK->value,
            CategoryMessage::DELETE_SUCCESSFULLY->value,
            $this->categoryService->delete($this->request->getParam('categoryId'), Session::get("userId"))
        );
    }

    /**
     * @throws HttpException
     */
    public function findOneCategory(): void
    {
        $this->response->response(
            StatusCode::OK->value,
            CategoryMessage::GET_CATEGORY_SUCCESSFULLY->value,
            $this->categoryService->findOne($this->request->getParam('categoryId'), Session::get("userId"))
        );
    }
}
