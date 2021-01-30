<?php

namespace App\Controller;

use Twig\Environment;
use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Renderer\Renderer;

class ConferenceController extends AbstractController
{
    private $twig;

    public function __construct(Renderer $twig)
    {
        $this->twig = $twig;
    }
    /**
     * @Route("/", name="homepage")
     */
    public function index(
        ConferenceRepository $conferenceRepository
    ): Response
    {
        return new Response($this->twig->render('conference/index.html.twig',
        [
            'conferences' => $conferenceRepository->findAll()
        ]));
    }

    /**
     * Undocumented function
     * @Route("/conference/{slug}", name="conference")
     * @return Response
     */
    public function show(
        Request $request,
        Conference $conference,
        CommentRepository $commentRepository
    ): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return new Response(
            $this->twig->render('conference/show.html.twig', [
                'conference' => $conference,
                'comments' => $paginator,
                'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
                'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            ]));
    }
}