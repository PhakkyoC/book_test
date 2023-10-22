<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class BookController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct (EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/', name: 'book_index')]
    public function index(): Response
    {
        $books = $this->em->getRepository(Book::class)->findAll();
        return $this->render('book/index.html.twig', [
            'books' => $books
        ]);
    }

    #[Route('/book/add', name: 'book_add')]
    public function addBook(Request $request){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        if($request->getMethod() == 'POST'){
            try{
                $book = new Book();
                $book->setTitle($request->get('title'));
                $book->setAuthor($request->get('author'));
                $book->setPublishedAt(new \DateTime($request->get('pdate')));
                $this->em->persist($book);
                $this->em->flush();
                return $this->render('book/book_form.html.twig', [
                    'message' => sprintf('Livre %s Ajouté', $book->getTitle()),
                    'message_type' => 'success'
                ]);
            }catch (\Exception $e){
                return $this->render('book/book_form.html.twig', [
                    'message' => $e->getMessage(),
                    'message_type' => 'error'
                ]);
            }
        }
        else{
            return $this->render('book/book_form.html.twig', [
            ]);
        }
    }

    #[Route('/book/edit/{id}', name: 'book_edit')]
    public function editBook($id, Request $request){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $book = $this->em->getRepository(Book::class)->find($id);
        if($request->getMethod() == 'POST'){
            try{
                $book->setTitle($request->get('title'));
                $book->setAuthor($request->get('author'));
                $book->setPublishedAt(new \DateTime($request->get('pdate')));
                $this->em->persist($book);
                $this->em->flush();
                return $this->render('book/book_form.html.twig', [
                    'message' => sprintf('Livre %s mis à jour', $book->getTitle()),
                    'message_type' => 'success',
                    'book' => $book
                ]);
            }catch (\Exception $e){
                return $this->render('book/book_form.html.twig', [
                    'message' => $e->getMessage(),
                    'message_type' => 'error',
                    'book' => $book
                ]);
            }
        }
        else{
            return $this->render('book/book_form.html.twig', [
                'book' => $book
            ]);
        }

    }

    #[Route('/book/delete/{id}', name: 'book_delete')]
    public function deleteBook($id){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        try{
            $book = $this->em->getRepository(Book::class)->find($id);
            $title = $book->getTitle();
            $this->em->remove($book);
            $this->em->flush(); 
            return $this->redirectToRoute('book_index');
        }
        catch (\Exception $e){
            return $this->redirectToRoute('book_index');
        }
    }
}
