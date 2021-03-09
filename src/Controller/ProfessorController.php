<?php

namespace App\Controller;

use App\Entity\Professor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfessorController extends AbstractController
{
    #[Route('/professor/create', name: 'create_professor')]
    public function create(ValidatorInterface $validator, Request $request): Response
    {
        // if ($request->isMethod('post')){

        $professor = new Professor();

        $professor->setNome($request->request->get('nome'));
        $professor->setIdade($request->request->getInt('idade'));
        $professor->setMatricula($request->request->getInt('matricula'));
        $professor->setAtivo($request->request->getBoolean('ativo'));


        $erros = $validator->validate($professor);

        if (count($erros) > 0) {
            return $this->render('professor/index.html.twig', [
                'errors' => $erros,
            ]);
        }


        $conexao = $this->getDoctrine()->getManager();
        $conexao->persist($professor);
        $conexao->flush();

        $this->addFlash('sucesso', 'Professor cadastrado');

        // }

        return $this->render('professor/index.html.twig', [
            'errors' => '',
        ]);


    }

    #[Route('/professor/list', name: 'list_professor')]
    public function list(): Response
    {
        $professores = $this->getDoctrine()->getRepository(Professor::class)->findAll();
        return $this->render('professor/list.html.twig', [
            'professores' => $professores,
        ]);
    }

    #[Route('/professor/{id}', name: 'professor')]
    public function index(Request $request,Professor $professor): Response
    {

        $form = $this->createFormBuilder($professor)
            ->add('Nome', TextType::class)
            ->add('Idade', TextType::class)
            ->add('Matricula', TextType::class, ['label' => 'Matrícula'])
            ->add('Ativo', CheckboxType::class, ['required' => false])
            ->add('salvar', SubmitType::class, ['label' => 'Salvar'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $professor = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($professor);
            $entityManager->flush();

            return $this->redirectToRoute('list_professor');
        }

        return $this->render('professor/form_professor.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('professor/show/{id}', name: 'show_professor')]
    public function show($id): Response
    {
        $professor = $this->getDoctrine()->getRepository(Professor::class)->find($id);
        return $this->render('professor/show.html.twig', [
            'professor' => $professor,
        ]);
    }
}
