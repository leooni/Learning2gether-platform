<?php
declare(strict_types=1);

namespace App\Controller;

use App\Domain\LanguageTrait;
use App\Domain\LearningModuleType;
use App\Entity\Language;
use App\Entity\LearningModule;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortalController extends AbstractController
{
    use LanguageTrait;

    /**
     * @Route("/portal", name="portal")
     */
    public function index(Request $request): Response
    {
        $modules = !isset($_GET['mode'])?
            $this->getDoctrine()->getRepository(LearningModule::class)->findBy(['isPublished' => true])
            : $this->getDoctrine()->getRepository(LearningModule::class)->findBy([
                'isPublished' => true,
                'type' => strtoupper($_GET['mode'])
            ]);


        if(!isset($_GET['mode'])) {
            $_GET['mode'] = LearningModuleType::HARD;
        }

        $modules = $this->getDoctrine()->getRepository(LearningModule::class)->findBy([
            'isPublished' => true,
            'type' => strtoupper($_GET['mode'])
        ]);

        $activeModules = $finishedModules = [];

        /** @var User $user */
        $user = $this->getUser();
        foreach($modules AS $learningModule) {
            if(isset($user->getBadges()[$learningModule->getId()])) {
                $finishedModules[] = $learningModule;
            } else {
                $activeModules[] = $learningModule;
            }
        }

        return $this->render('portal/index.html.twig', [
            'language' => $this->getLanguage($request),
            'activeModules' => $activeModules,
            'finishedModules' => $finishedModules,
        ]);
    }
}
