<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Entity\User;
use App\Entity\CDRom;
use App\Entity\Livre;
use App\Form\LoanType;
use App\Repository\CDRomRepository;
use App\Repository\LoanRepository;
use App\Repository\LivreRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/loan")
 */
class LoanController extends AbstractController
{
    /**
     * @Route("/", name="loan_index", methods={"GET"})
     */
    public function index(LoanRepository $loanRepository): Response
    {
        return $this->render('loan/index.html.twig', [
            'loans' => $loanRepository->findAll(),
        ]);
    }

    /**
     * Allows you to renew a loan
     * @Route("/renew" , name="loan_renew")
     */
    public function renew(Loan $loan, User $user)
    {
        $loan->setUdatedAt(new \DateTime());
        return $this->redirectToRoute('user_show', [
            'id' => $user,
        ]);
    }

    /**
     * @Route("/new", name="loan_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $loan = new Loan();
        $form = $this->createForm(LoanType::class, $loan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //make the books unavailable
            $livres = $form->getData()->getLivres();
            foreach ($livres as $key => $livre) {
                $livre->setAvailability(false);
            }

            //make the cdrom unavailable
            $cdroms = $form->getData()->getCdrom();
            foreach ($cdroms as $key => $cdrom) {
                $cdrom->setAvailability(false);
            }

            #//$loan->setCreatedAt(new \DateTime());
            #//2) Mettre l'emprunt non disponible
            #//3) Enregistrer la date de création de l'emprunt
            //4) Mettre le statut à jour
            //5) Vérifier si l'emprunt est un renouvellement (updatedAt)
            //6) Vérifier qu'il peut encore emprunter pas plus de 5 emprunts
            //7) Vérifier qu'il posséde la caution
            //8) Vérifier que la ressource n'a pas atteint la date limite d'emprunt

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($loan);
            $entityManager->flush();

            return $this->redirectToRoute('loan_index');
        }

        return $this->render('loan/new.html.twig', [
            'loan' => $loan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="loan_show", methods={"GET"})
     */
    public function show(Loan $loan): Response
    {
        return $this->render('loan/show.html.twig', [
            'loan' => $loan,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="loan_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Loan $loan,
        LivreRepository $LivreRepository,
        CDRomRepository $CDRomRepository
    ): Response {
        $form = $this->createForm(LoanType::class, $loan);
        $form->handleRequest($request);

        if (isset($_GET['renew'])) {
            $livre = $LivreRepository->findOneById($_GET['livre']);
            //dd($livre);
            $livre->setUpdatedAt(new \DateTime());
            return $this->redirectToRoute('user_show', [
                'id' => $_GET['user'],
            ]);
        } elseif (isset($_GET['livre'])) {
            $this->addFlash('success', 'votre action a fonctionnée ');
            $livre = $LivreRepository->findOneById($_GET['livre']);
            //availability changer function
            $loan->AvailabilityLivre($livre);

            //sortir le livre de l'emprunt
            $loan->removeLivre($livre);

            $this->getDoctrine()
                ->getManager()
                ->flush();
            //redirection vers le user_show
            return $this->redirectToRoute('user_show', [
                'id' => $_GET['user'],
            ]);
        }

        if (isset($_GET['cdrom'])) {
            $this->addFlash('success', 'votre action a fonctionnée ');

            //rendre le livre disponible
            $cdrom = $CDRomRepository->findOneById($_GET['cdrom']);
            // dd($livre);

            $cdrom->setAvailability(true);

            //sortir le livre de l'emprunt
            $loan->removeCDRom($cdrom);

            $this->getDoctrine()
                ->getManager()
                ->flush();
            //redirection vers le user_show
            return $this->redirectToRoute('user_show', [
                'id' => $_GET['user'],
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()
                ->getManager()
                ->flush();

            return $this->redirectToRoute('loan_index');
        }

        return $this->render('loan/edit.html.twig', [
            'loan' => $loan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="loan_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Loan $loan): Response
    {
        if (
            $this->isCsrfTokenValid(
                'delete' . $loan->getId(),
                $request->request->get('_token')
            )
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($loan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('loan_index');
    }
}
