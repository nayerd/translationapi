## Translation basket checkout API

This project implements a API module to calculate the translation price for a given basket that contains documents to be translated into other languages.
This module can be part of a checkout process  

## Installing:

1. Clone repository: `git clone https://github.com/nayerd/translationapi.git`.
2. Move to project folder: `cd translationapi`.
3. Duplicate .env.example file, rename to .env and set your variables. (Check env.testing for testing environment)
4. Install project dependencies: `composer install`.
5. Execute deploy: `php artisan deploy`.
6. The script can create some example data (will ask to the user).

## Tests:
Run: `php artisan test`.

## API endpoints:
You can use Postman to test and use the endpoints. There is a Postman file that contains the collection of the endpoints.
1. Go to the project folder
2. Find the file translationapi.postman_collection.json 
3. Open the file with Postman

```
Create a new basket:
POST - http://127.0.0.1:8000/api/basket
Body object:
{
  "project_id": "ABC",
  "customer_id": "23456",
  "expected_due_date": "2022-12-31",
  "target_languages" : [
      "es_ES",
      "en_GB",
      "ca_ES",
  ]
}

-------------------------------------------

Add file to the basket:
POST - http://127.0.0.1:8000/api/basket_document
Body object:
{
    "project_id": "ABC",
    "file_id": "text_file_1",
    "file_name": "translation_file_name",
    "file_type": "txt",
    "file_content": "This is the content of the file#LW-Test#This is another sentence#LW-Test#This is part of the content#LW-Test#This is the content of the file",
    "comments": "This is a comment for the given file",
}

-------------------------------------------

Get basket data:
GET - http://127.0.0.1:8000/api/basket/{project_id}

-------------------------------------------

## Challenge description  
We want to provide a REST API to satisfy the following business requirements gathered from our user story mapping session:

**Translation basket (basket) creation:**   
We can create a basket by specifying a project id, a customer id, a list of target languages (language codes) and an expected due date.
Project id will be the identifier for this basket.  
  
**Add files to translate to the basket:**  
We now attach the files we already uploaded using another API to the project.
In order to do so we add to the project with a `project id` the file entities providing the following for each file: `file id`, `file name`, `file type`, `file content`, and `comments`.  
The "file type" will be the extension of the file.  
The "file content" consist of the plain sentences that are inside the file splitted by the token '#LW-Test#'.   
The comments are only for the consideration of the translator once it gets there.  

**View a basket:**  
At any time, the API client should be able to check what's inside the basket with certain id.  
Our endpoint would return: 
The project id, the customer id, the target languages, the due date, the remaining time for the due date. And...  
The list of files indicating the id, name, type, and comments. And...   
The calculated price for the translation based on items we have so far.
 
### Acceptance criteria for price calculation 
- Standard price per word is 0,07€  
- When a word is repeated inside the same file the price is 0,02€.
- When a full sentence is repeated in the same file the price is 0 for the whole sentence.
- When a word is repeated in another file the price is 0,05€.
- When a full sentence is repeated in another file the price is 0,01€ for each word.
- For PDF type formats the price is 20% more than the standard one.
- For PSD type formats (Photoshop) the price is 35% more than the standard one.
- The target language "es-ES" gets a 20% discount on the total price.


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
