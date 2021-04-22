<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context\Setup;

use App\Fixture\Factory\AdminUserExampleFactory;
use Behat\Behat\Context\Context;
use Monofony\Bridge\Behat\Service\AppSecurityServiceInterface;
use Monofony\Bridge\Behat\Service\SharedStorageInterface;
use Monofony\Contracts\Core\Model\User\AppUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Webmozart\Assert\Assert;

final class AppSecurityContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private AppSecurityServiceInterface $securityService;
    private AdminUserExampleFactory $userFactory;
    private UserRepositoryInterface $appUserRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        AppSecurityServiceInterface $securityService,
        AdminUserExampleFactory $userFactory,
        UserRepositoryInterface $appUserRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->securityService = $securityService;
        $this->userFactory = $userFactory;
        $this->appUserRepository = $appUserRepository;
    }

    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs($email): void
    {
        $user = $this->appUserRepository->findOneByEmail($email);
        Assert::notNull($user);

        $this->securityService->logIn($user);
    }

    /**
     * @Given I am a logged in customer
     */
    public function iAmLoggedInAsACustomer(): void
    {
        /** @var AppUserInterface $user */
        $user = $this->userFactory->create(['email' => 'customer@example.com', 'password' => 'password', 'roles' => ['ROLE_USER']]);
        $this->appUserRepository->add($user);

        $this->securityService->logIn($user);

        $this->sharedStorage->set('customer', $user->getCustomer());
    }
}
