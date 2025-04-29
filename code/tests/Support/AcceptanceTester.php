<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Domain\Entity\User;

/**
 * Inherited Methods
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */
    public function grabJwtToken(string $username = 'user@example.com', string $password = 'password123'): string
    {


        $user = new User();
        $user->setLogin('admin');
        $user->setFullName('admin');
        $user->setPassword('$2y$13$0VQdXo0zn0yki8CLuJaDpeAhmT0wRYUWSxAHNSsbCVx1zeODo4Xt2');
        $user->setPhone('7056735855');
        $user->setEmail('albina@mail.ru');
        $user->setAge(19);
        $user->setIsActive('true');
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setCreatedAt();
        $user->setUpdatedAt();

        /** @var EntityManagerInterface $em */
        $em = $this->grabService('doctrine')->getManager();

        $em->persist($user);
        $em->flush();



        $authHeader = 'Basic ' . base64_encode("$username:$password");

        $this->haveHttpHeader('Authorization', $authHeader);
        $this->sendPOST('/api/token');

        $this->seeResponseCodeIs(200);
        $this->seeResponseIsJson();
        $response = json_decode($this->grabResponse(), true);

        if (!isset($response['token'])) {
            throw new \Exception('Не удалось получить токен');
        }

        return $response['token'];
    }

}
