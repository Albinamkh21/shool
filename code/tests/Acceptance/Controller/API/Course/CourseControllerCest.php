<?php
/*
Author
*/

namespace Acceptance\Controller\API\Course;

use App\Tests\Support\AcceptanceTester;

use Symfony\Component\HttpFoundation\Response;

class CourseControllerCest
{
    private string $login;

    private string $password;
    
    private string $token;

    public function _before(AcceptanceTester $I)
    {
        $this->login = $_ENV['TEST_ADMIN_LOGIN'];
        $this->password = $_ENV['TEST_ADMIN_PASSWORD'];
        $this->token = $I->grabJwtToken($this->login, $this->password);
    }

    // Positive test case
    public function testCreate(AcceptanceTester $I)
    {
       
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);

        $data = [
            'title' => 'New Test Course',
            'description' => 'Course description',
            'price' => '1000',

        ];
        $I->sendPost('/api/course', $data);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(['title' => 'New Test Course']);

    }

    // Negative test cases for create
    public function testCreateWithoutTitle(AcceptanceTester $I)
    {
       // $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);

        $data = [
            'description' => 'Course description',
            'price' => '1000',
        ];
        $I->sendPost('/api/course', $data);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['error' => 'Invalid request data']);
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
        $I->seeResponseContainsJson(['error' => 'Unauthorized']);
    }
*/
    // Positive test case
    public function testDeleteCourse(AcceptanceTester $I)
    {
       // $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);

        $I->sendDELETE('/api/course', ['id' => 1]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);
    }

    // Negative test cases for delete
    public function testDeleteCourseWithoutId(AcceptanceTester $I)
    {
        //$token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);

        $I->sendDELETE('/api/course');

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['error' => 'ID is required']);
    }
    public function testDeleteCourseWithInvalidId(AcceptanceTester $I)
    {
        //$token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);

        $I->sendDELETE('/api/course', ['id' => '999999']);

        $I->seeResponseCodeIs(Response::HTTP_NOT_FOUND);

        $I->seeResponseIsJson();
        $I->seeResponseEquals('{}');

    }

    /*
    public function testDeleteCourseWithoutAuthorization(AcceptanceTester $I)
    {
        $I->sendDELETE('/api/lesson', ['id' => 7]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['error' => 'Unauthorized']);
    }
*/
    // Positive test case
    public function testGetLesson(AcceptanceTester $I)
    {
        //$token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);

        $I->sendGET('/api/course', ['id' => 1]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['id' => 1]);
    }

    // Negative test cases for get
    public function testGetCourseWithoutId(AcceptanceTester $I)
    {
        //$token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);

        $I->sendGET('/api/course');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $response = $I->grabResponse();
        $data = json_decode($response, true);
        $I->assertGreaterThan(0, count($data))  ;

    }

    public function testGetLessonWithInvalidId(AcceptanceTester $I)
    {
        //$token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);

        $I->sendGET('/api/course', ['id' => '9999999']);

        $I->seeResponseCodeIs(Response::HTTP_NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('{}');
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

    // Positive test case
    public function testUpdateCourse(AcceptanceTester $I)
    {
        //$token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);

        $courseId = 1; // Assuming this course ID exists in your database
        $I->sendPATCH("/api/course/{$courseId}", [
            'description' => ' updated description',
            'title' => 'Updated Course 6 ',
            'price' => '3000',
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);
    }

    // Negative test cases for update
    public function testUpdateCourseWithoutId(AcceptanceTester $I)
    {
        //$token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);

        $I->sendPATCH('/api/course', [
            'description' => ' updated description',
            'title' => 'Updated Course 6 ',
            'price' => '3000',

        ]);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::METHOD_NOT_ALLOWED);
    }

    public function testUpdateCourseWithInvalidId(AcceptanceTester $I)
    {
       // $token = $I->grabJwtToken($this->login, $this->password);
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token);

        $courseId = 999999;
        $I->sendPATCH("/api/course/{$courseId}", [

            'description' => ' updated description',
            'title' => 'Updated Course 6 ',
            'price' => '3000',

        ]);

        $I->seeResponseCodeIs(Response::HTTP_NOT_FOUND);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('{}');
    }
/*
    public function testUpdateLessonWithoutAuthorization(AcceptanceTester $I)
    {
        $I->sendPATCH('/api/lesson', [
            'id' => 4,
            'title' => 'Updated Lesson 4 for 3 course',
            'description' => 'Lesson Description',
            'courseId' => 3,
            'order' => 1,
            'contents' => json_encode(['text' => 'Updated Lesson content'])
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['error' => 'Unauthorized']);
    }
*/
}