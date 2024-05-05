<?php

namespace App\Controller;

use App\Entity\Moto;
use App\Form\MotoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MotoController extends AbstractController
{
    #[Route('/', name: 'moto')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $motos = $em->getRepository(Moto :: class)->findAll();
        $em->flush();


        return $this->render('moto/index.html.twig', [
            'motos' => $motos
        ]);
    }
    #[Route('/moto/create', name: 'moto_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $moto = new Moto;
        $form = $this->createForm(MotoType :: class,$moto);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($moto);
            $em->flush();
            $this->addFlash('success', "La moto ".$moto->getNom()." a été ajoutée avec succès");
            return $this->redirectToRoute('moto');
        }
        return $this->render('moto/create.html.twig',[
            'form' => $form
        ]);
    }
    #[Route('/moto/{id}/details', name:'moto_details' ,requirements: ['id' => '\d+'])]
    public function details(Moto $moto, EntityManagerInterface $em, int $id)
    {
        $moto = $em->getRepository(Moto :: class)->find($id);
        return $this->render('moto/details.html.twig',[
           'moto' => $moto
        ]);
    }
    #[Route('/moto/{id}/edit', name:'moto_edit' )]
    public function edit(Moto $moto, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(MotoType :: class,$moto);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', "La moto ".$moto->getNom()." a été modifiée avec succès");
            return $this->redirectToRoute('moto_details',['id' => $moto->getId()]);
        }
        return $this->render('moto/edit.html.twig',[
            'form' => $form
        ]);
    }
    #[Route('/moto/{id}/delete', name:'moto_delete' )]
    public function delete(Moto $moto, EntityManagerInterface $em): Response
    {
        $nom = $moto->getNom();
        $em->remove($moto);
        $em->flush();
        $this->addFlash('danger', "La moto '$nom' a été supprimée avec succès");
        return $this->redirectToRoute('moto');
    }
}
