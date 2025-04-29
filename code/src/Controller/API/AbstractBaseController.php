<?php


namespace App\Controller\API;

use App\Controller\Traits\TranslatorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;


abstract class AbstractBaseController extends AbstractController
{
    private ?TranslatorInterface $translator = null;

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    protected function trans(string $key, array $parameters = [], string $domain = null, string $locale = null): string
    {
        if (!$this->translator) {
            throw new \LogicException('Translator has not been set. Ensure it is passed via setTranslator().');
        }

        return $this->translator->trans($key, $parameters, $domain, $locale);
    }
}
