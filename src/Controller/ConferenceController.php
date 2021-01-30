<?php

namespace App\Controller;

use App\Entity\Comment;
use Twig\Environment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Renderer\Renderer;
use Doctrine\ORM\EntityManagerInterface;

class ConferenceController extends AbstractController
{
    private $twig;
    private $em;

    public function __construct(
        Renderer $twig,
        EntityManagerInterface $em
        )
    {
        $this->twig = $twig;
        $this->em = $em;
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

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);

            $this->em->persist($comment);
            $this->em->flush();

            return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return new Response(
            $this->twig->render('conference/show.html.twig', [
                'conference' => $conference,
                'comments' => $paginator,
                'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
                'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
                'comment_form' => $form->createView()
            ]));
    }
}