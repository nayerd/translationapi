<?php

namespace App\Services;

use App\Models\Basket;
use App\Models\BasketDocument;
use App\Models\BasketTargetLanguage;
use App\Models\Document;
use App\Models\Language;

use DateTime;

class BasketService
{
    /**
     * Customer service internal variable
     *
     * @var CustomerService
     */
    protected $customerService;

    /**
     * Project service internal variable
     *
     * @var ProjectService
     */
    protected $projectService;

    /**
     * Default class constructor
     *
     * @param ProjectService $projectService
     * @param CustomerService $customerService
     */
    public function __construct(ProjectService $projectService, CustomerService $customerService)
    {
        $this->projectService = $projectService;
        $this->customerService = $customerService;
    }

    /**
     * Creates the basket model object from the given parameters
     *
     * @param string|null $projectId
     * @param string|null $customerId
     * @param array|null $targetLanguages
     * @param DateTime $expectedDueDate
     * @return Basket|null
     */
    public function createBasket(string $projectId = null, string $customerId = null, array $targetLanguages = null, DateTime $expectedDueDate): ?Basket
    {
        //Call the service to create the project and the customer
        $project = $this->projectService->createProject($projectId);
        $customer = $this->customerService->createCustomer($customerId);

        //Overwrite if there is an existing basket
        $basket = Basket::whereProjectId($project->id)->whereCustomerId($customer->id)->first();

        $basket = Basket::updateOrCreate(
            [
                'id' => !empty($basket) ? $basket->id : null
            ],
            [
                'project_id'    => $project->id,
                'customer_id'   => $customer->id,
                'due_date'      => $expectedDueDate
            ]
        );

        //Delete previous target languages
        BasketTargetLanguage::whereBasketId($basket->id)->delete();

        //Add the languages
        foreach ($targetLanguages as $language) {
            $language = Language::whereIso($language)->first();

            BasketTargetLanguage::updateOrCreate([
                'basket_id'     => $basket->id,
                'language_id'   => !empty($language) ? $language->id : null,
            ]);
        }

        //Update the translation price, this action can be made using BasketTargetLanguageObserver
        if(config('translationapi.enable_observers') == false) {
            $basket->updateTranslationPrice();
        }

        return $basket;
    }

    /**
     * Add a given document to the given basket
     *
     * @param string|null $projectId
     * @param string|null $fileId
     * @param string|null $fileName
     * @param string|null $fileType
     * @param string|null $fileContent
     * @param string|null $comments
     * @return Basket|null
     */
    public function addDocument(string $projectId = null, string $fileId = null, string $fileName = null, string $fileType = null, string $fileContent = null, string $fileComments = null): ?Basket
    {
        $project = $this->projectService->createProject($projectId);

        if (empty($project)) {
            return null;
        }

        $basket = $project->baskets()->whereProjectId($project->id)->first();

        if (empty($basket)) {
            return null;
        }

        $document = Document::updateOrCreate(
            [
                'file_id'           => $fileId
            ],
            [
                'file_id'           => $fileId,
                'file_name'         => $fileName,
                'file_type'         => $fileType,
                'file_content'      => $fileContent,
                'file_comments'     => $fileComments
            ]
        );

        BasketDocument::updateOrCreate([
            'basket_id'             => $basket->id,
            'document_id'           => $document->id
        ]);

        //Update the translation price, This action can be performed using BasketDocumentObserver
        if(config('translationapi.enable_observers') == false) {
            $basket->updateTranslationPrice();
        }

        return $basket;
    }

    /**
     * Gets the basket by the given project id
     *
     * @param string $projectId
     * @return Basket|null
     */
    public function getBasket(string $projectId): ?Basket
    {
        $project = $this->projectService->createProject($projectId);
        if (empty($project)) {
            return null;
        }

        return Basket::whereProjectId($project->id)->first();
    }
}
