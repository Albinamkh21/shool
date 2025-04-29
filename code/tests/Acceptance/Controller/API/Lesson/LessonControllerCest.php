<?php


namespace Acceptance\Controller\API\Lesson;

use App\Tests\Support\AcceptanceTester;
use http\Env\Request;


class LessonControllerCest
{
    private string $login;

    private string $password;

    public function _before(AcceptanceTester $I)
    {
        $this->login = $_ENV['TEST_ADMIN_LOGIN'];
        $this->password = $_ENV['TEST_ADMIN_PASSWORD'];
    }
    
    public function testCreate(AcceptanceTester $I)
    {

        $token = $I->grabJwtToken($this->login, $this->password);

        // Устанавливаем токен в заголовках для следующих запросов
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendPOST('/api/lesson', [
            'title' => 'New Lesson',
            'description' => 'Lesson Description',
            'courseId' => 14,
            'order' => 1,
            'contents' => json_encode(['text' => 'Lesson content'])
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['title' => 'New Lesson']);

        $response = $I->grabResponse();
       // codecept_debug($response);
       // $I->seeResponseContainsJson(['success' => true]);
    }

    public function testCreateWithoutTitle(AcceptanceTester $I)
    {
        $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendPOST('/api/lesson', [
            'description' => 'Lesson Description',
            'courseId' => 14,
            'order' => 1,
            'contents' => json_encode(['text' => 'Lesson content'])
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['errors' => 'array']);
        $response = json_decode($I->grabResponse(), true);
        $I->assertContains('Заголовок урока не должен быть пустым.', $response['errors']);


    }

    public function testCreateWithInvalidCourseId(AcceptanceTester $I)
    {
        $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendPOST('/api/lesson', [
            'title' => 'New Lesson',
            'description' => 'Lesson Description',
            'courseId' => 'invalid',
            'order' => 1,
            'contents' => json_encode(['text' => 'Lesson content'])
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['errors' => 'array']);
        $response = json_decode($I->grabResponse(), true);
        $errorsString = implode(' ', $response['errors']);
        $I->assertStringContainsString('не должен быть', $errorsString);


    }

    /*
    public function testCreateWithoutAuthorization(AcceptanceTester $I)
    {
        $I->sendPOST('/api/lesson', [
            'title' => 'New Lesson',
            'description' => 'Lesson Description',
            'courseId' => 14,
            'order' => 1,
            'contents' => json_encode(['text' => 'Lesson content'])
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
       // $I->seeResponseContainsJson(['error' => '%Invalid JWT Token%']);
        $I->seeResponseContains('Invalid JWT Token');
    }
*/
    public function testDeleteLesson(AcceptanceTester $I)
    {
        $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendDELETE('/api/lesson', ['id' => 13]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);
    }
    public function testDeleteLessonWithoutId(AcceptanceTester $I)
    {
        $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendDELETE('/api/lesson');

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['errors' => 'ID is required']);
    }
    public function testDeleteLessonWithInvalidId(AcceptanceTester $I)
    {
        $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendDELETE('/api/lesson', ['id' => '999999999']);

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['errors' => 'Invalid ID']);
    }
/*
    public function testDeleteLessonWithoutAuthorization(AcceptanceTester $I)
    {
        $I->sendDELETE('/api/lesson', ['id' => 7]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['error' => 'Unauthorized']);
    }
*/
    public function testGetLesson(AcceptanceTester $I)
    {
        $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendGET('/api/lesson', ['id' => 4]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['id' => 4]);
    }

    public function testGetLessonWithInvalidId(AcceptanceTester $I)
    {
        $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendGET('/api/lesson', ['id' => '999999999']);

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['errors' => 'Invalid ID']);
    }

    /*
       public function testGetLessonWithoutAuthorization(AcceptanceTester $I)
    {
        $I->sendGET('/api/lesson', ['id' => 4]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['error' => 'Unauthorized']);
    }
    */
    public function testUpdateLessonTitle(AcceptanceTester $I)
    {
        $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendPATCH('/api/lesson', [
             'id' => 4,
            'title' => 'Updated Lesson 4 for 3 course',
            'description' => 'Lesson Description',
            'courseId' => 3,
            'order' => 1,
            'contents' => json_encode(['text' => 'Updated Lesson content'])
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);
    }
    public function testUpdateLessonWithoutId(AcceptanceTester $I)
    {
        $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendPATCH('/api/lesson', [
            'title' => 'Updated Lesson 4 for 3 course',
            'description' => 'Lesson Description',
            'courseId' => 3,
            'order' => 1,
            'contents' => json_encode(['text' => 'Updated Lesson content'])
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['errors' => 'ID is required']);
    }


    public function testUpdateLessonWithInvalidId(AcceptanceTester $I)
    {
        $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $token);

        $I->sendPATCH('/api/lesson', [
            'id' => '99999',
            'title' => 'Updated Lesson 4 for 3 course',
            'description' => 'Lesson Description',
            'courseId' => 3,
            'order' => 1,
            'contents' => json_encode(['text' => 'Updated Lesson content'])
        ]);

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['errors' => 'Invalid ID']);
    }







}

