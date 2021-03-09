<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Curso;

class CursoController extends AbstractController
{
    #[Route('/curso', name: 'curso')]
    public function index(): Response
    {
        return $this->render('curso/index.html.twig', [
            'errors' => '',
        ]);
    }

    #[Route('/curso_new', name: 'curso_new')]
    public function create(Request $request, ValidatorInterface $validator)
    {
        $curso = new Curso();
        $curso->setNome($request->request->get('nome'));
        $curso->setHoras($request->request->getInt('horas'));
        $curso->setAtivo($request->request->getBoolean('ativo'));

        $errors = $validator->validate($curso);

        if (count($errors) > 0) {
            return $this->render('curso/index.html.twig', [
                'errors' => $errors,
            ]);

        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($curso);
        $entityManager->flush();

        $this->addFlash('sucesso', 'Cadastrado com sucesso');

        return $this->redirectToRoute('curso_list');
    }

    #[Route('/curso/list', name: 'curso_list')]
    public function list(): Response
    {

        $cursos = $this->getDoctrine()->getRepository(Curso::class)
            ->findAll();

        return $this->render('curso/list.html.twig', [
            'cursos' => $cursos,
        ]);
    }

    #[Route('/curso/list/{ativo}', name: 'curso_list_ativo')]
    public function listAtivo($ativo): Response
    {

        $cursos = $this->getDoctrine()->getRepository(Curso::class)
            ->findAllAtivos($ativo);

        return $this->render('curso/list.html.twig', [
            'cursos' => $cursos,
        ]);
    }

    #[Route('/curso/edit/{id}', name: 'curso_edit')]
    public function update(ValidatorInterface $validator, Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $curso = $entityManager->getRepository(Curso::class)->find($id);

        if (!$curso) {
                return $this->render('curso/index.html.twig', [
                    'errors' => 'Curso não encontrado',
                ]);
        }

        if ($request->isMethod('post')){
            #TODO: new list of attributes could be provided as request object
            $curso->setNome($request->request->get('nome'));
            $curso->setHoras($request->request->getInt('horas'));
            $curso->setAtivo($request->request->getBoolean('ativo'));

            $errors = $validator->validate($curso);

            if (count($errors) > 0) {
                return $this->render('curso/edit.html.twig', [
                    'errors' => $errors,
                    'curso'  => $curso,
                ]);

            }

            $entityManager->flush();
            $thphpis->addFlash('sucesso', 'Editado com sucesso');
            return $this->redirectToRoute('curso_list');
        }

        return $this->render('curso/edit.html.twig', [
            'curso' => $curso,
            'errors' => '',
        ] );
    }

    #[Route('/curso/delete/{id}', name: 'curso_delete')]
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $curso = $entityManager->getRepository(Curso::class)->find($id);
        $entityManager->remove($curso);
        $entityManager->flush();
        $this->addFlash('sucesso', "Deletado {$curso->getNome()} com sucesso");
        return $this->redirectToRoute('curso_list');

    }

}
