<?php

namespace Tests\Acceptance;

use App\Models\Basket;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class BasketTest extends TestCase
{
    /**
     * Initial setup of the test suite
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * Calls to the basket endpoint to create a basket
     *
     * @param string $projectId
     * @param string $customerId
     * @param string $expectedDueDate
     * @param array $targetLanguages
     * @return TestResponse
     */
    protected function createBasket(
        string $projectId = 'ABC',
        string $customerId = '123',
        string $expectedDueDate = '2022-12-31',
        array $targetLanguages = ['es_ES']
    ): TestResponse {
        return $this->postJson('/api/basket', [
            'project_id'        => $projectId,
            'customer_id'       => $customerId,
            'expected_due_date' => $expectedDueDate,
            'target_languages'  => $targetLanguages,
        ]);
    }

    /**
     * Calls to the basket document endpoint to add a new document to the given basket
     *
     * @param string $projectId
     * @param string $fileId
     * @param string $fileName
     * @param string $fileType
     * @param string|null $fileContent
     * @param string|null $comments
     * @return TestResponse
     */
    protected function addDocumentToBasket(
        string $projectId = 'ABC',
        string $fileId = 'text_file_1',
        string $fileName = 'translation_name',
        string $fileType = 'txt',
        string $fileContent = null,
        string $comments = null
    ): TestResponse {
        return $this->postJson('/api/basket_document', [
            'project_id'    => $projectId,
            'file_id'       => $fileId,
            'file_name'     => $fileName,
            'file_type'     => $fileType,
            'file_content'  => !empty($fileContent) ? $fileContent : 'This is the content of the file#LW-Test#This is another sentence#LW-Test#This is the content#LW-Test#This is the content of the file',
            'comments'      => !empty($comments) ? $comments : 'This is the comment of the file',
        ]);
    }

    /**
     * Check that the endpoints are working
     *
     * @return void
     */
    public function test_endpoints_returns_a_successful_response()
    {
        $response = $this->createBasket();
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'created'   => true
        ]);

        $addFileToBasketResponse = $this->addDocumentToBasket();
        $addFileToBasketResponse->assertStatus(200);

        $getBasketResponse = $this->getJson('/api/basket/ABC');
        $getBasketResponse->assertStatus(200);
    }

    /**
     * Checks the basket endpoint
     *
     * @return void
     */
    public function test_create_basket_works_as_expected()
    {
        //Create basket
        $response = $this->createBasket();
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'created'   => true
        ]);

        //Data is present in the database
        $this->assertDatabaseCount('baskets', 1);
        $this->assertDatabaseCount('basket_target_languages', 1);
    }

    /**
     * Checks the basket document endpoint
     *
     * @return void
     */
    public function test_basket_document_works_as_expected()
    {
        //Create the basket
        $response = $this->createBasket();
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'created'   => true
        ]);

        //Add document
        $addFileToBasketResponse = $this->addDocumentToBasket();
        $addFileToBasketResponse->assertStatus(200);
        $response->assertJsonFragment([
            'created'   => true
        ]);

        //Document is present and the price is not 0
        $getBasketResponse = $this->getJson('/api/basket/ABC');
        $getBasketResponse->assertStatus(200);
        $getBasketResponse->assertJsonMissing([
            'calculated_price'  => "0 €"
        ]);

        //Data is present in the database
        $this->assertDatabaseCount('basket_documents', 1);
    }

    /**
     * Checks that the price updates when a new target language is added
     *
     * @return void
     */
    public function test_when_adding_target_language_the_price_updates()
    {
        //Create the basket
        $response = $this->createBasket();
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'created'   => true
        ]);

        $this->addDocumentToBasket();

        //Document is present and the price is not 0
        $getBasketResponse = $this->getJson('/api/basket/ABC');
        $getBasketResponse->assertStatus(200);
        $getBasketResponse->assertJsonMissing([
            'calculated_price'  => "0 €"
        ]);

        $price = (Basket::first())->basket_price;

        //Update the basket
        $response = $this->createBasket('ABC', '123', '2022-12-31', ['es_ES', 'en_GB', 'ca_ES']);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'created'   => true
        ]);

        //Get the actual basket
        $getBasketResponse = $this->getJson('/api/basket/ABC');
        $getBasketResponse->assertStatus(200);
        $getBasketResponse->assertJsonMissing([
            'calculated_price'  => round($price, 2) . ' €'
        ]);
    }

    /**
     * Checks that the price updates when addind a new document
     *
     * @return void
     */
    public function test_when_adding_document_the_price_updates()
    {
        //Create the basket
        $this->createBasket();
        $this->addDocumentToBasket();
        $price = (Basket::first())->basket_price;

        //Add a second document
        $this->addDocumentToBasket(
            'ABC',
            'text_file_2',
            'translation_name_2',
            'pdf',
            'This is the content of the new file#LW-Test#A second file that should change the translation price.',
            'This is the comment of the second file'
        );

        //Get the basket
        $getBasketResponse = $this->getJson('/api/basket/ABC');
        $getBasketResponse->assertStatus(200);
        $getBasketResponse->assertJsonMissing([
            'calculated_price'  => round($price, 2) . ' €'
        ]);
    }

    /**
     * Checks if the calculated price is working as expected
     *
     * @return void
     */
    public function test_price_calculation()
    {
        //Single file with no repeated words
        $this->createBasket('ABC', '123', '2022-12-31', ['en_GB']);
        $this->addDocumentToBasket('ABC', 'text_file_1', 'translation_name', 'txt', 'This is the file content.', 'Comments');
        $getBasketResponse = $this->getJson('/api/basket/ABC');
        $getBasketResponse->assertJsonFragment([
            'calculated_price'  => '0.35 €'
        ]);

        //Single file with repeated words
        $this->createBasket('ABCD', '123', '2022-12-31', ['en_GB']);
        $this->addDocumentToBasket('ABCD', 'text_file_2', 'translation_name_2', 'txt', 'This is the file content and this file has repeated words.', 'Comments');
        $getBasketResponse = $this->getJson('/api/basket/ABCD');
        $getBasketResponse->assertJsonFragment([
            'calculated_price'  => '0.67 €'
        ]);

        //Single file with repeated sentence and words
        $fileContent = 'This is the file content and this file has repeated words.' . config('translationapi.sentence_separator') .
            'This is the file content and this file has repeated words.';

        $this->createBasket('ABCDE', '123', '2022-12-31', ['en_GB']);
        $this->addDocumentToBasket('ABCDE', 'text_file_3', 'translation_name_3', 'txt', $fileContent, 'Comments');
        $getBasketResponse = $this->getJson('/api/basket/ABCDE');
        $getBasketResponse->assertJsonFragment([
            'calculated_price'  => '0.67 €'
        ]);

        //Multiple file
        $fileContent = 'This is the file content and this file has repeated words.' . config('translationapi.sentence_separator') .
            'Another sentence that has repeated words.';
        $this->addDocumentToBasket('ABCDE', 'text_file_4', 'translation_name_4', 'txt', $fileContent, 'Comments');
        $getBasketResponse = $this->getJson('/api/basket/ABCDE');
        $getBasketResponse->assertJsonFragment([
            'calculated_price'  => '2.37 €'
        ]);
    }

    /**
     * When the selected target translation language is es_ES then apply 20% off
     *
     * @return void
     */
    public function test_price_discount_by_language()
    {
        $this->createBasket('ABC', '123', '2022-12-31', ['es_ES']);
        $this->addDocumentToBasket('ABC', 'text_file_1', 'translation_name', 'txt', 'This is the file content.', 'Comments');
        $getBasketResponse = $this->getJson('/api/basket/ABC');
        $getBasketResponse->assertJsonFragment([
            'calculated_price'  => '0.28 €'
        ]);
    }

    /**
     * PSD and PDF File types are increasing the final price
     *
     * @return void
     */
    public function test_price_increases_by_document_file_type()
    {
        //Check that pdf and psd have different pricing
        $this->createBasket('ABC', '123', '2022-12-31', ['en_GB']);
        $this->addDocumentToBasket('ABC', 'text_file_1', 'translation_name', 'pdf', 'This is the file content.', 'Comments');
        $getBasketResponse = $this->getJson('/api/basket/ABC');
        $getBasketResponse->assertJsonFragment([
            'calculated_price'  => '0.42 €'
        ]);

        $this->createBasket('ABCD', '123', '2022-12-31', ['en_GB']);
        $this->addDocumentToBasket('ABCD', 'text_file_2', 'translation_name', 'psd', 'This is the file content.', 'Comments');
        $getBasketResponse = $this->getJson('/api/basket/ABCD');
        $getBasketResponse->assertJsonFragment([
            'calculated_price'  => '0.47 €'
        ]);
    }
}
