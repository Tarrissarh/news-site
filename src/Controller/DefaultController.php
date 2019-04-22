<?php

namespace App\Controller;

use \App\Entity\Post;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\Request;
use \Knp\Component\Pager\PaginatorInterface;

class DefaultController extends AbstractController
{
	/**
	 * @Route("/", name="default", methods={"GET"})
	 * @param Request $request
	 * @param PaginatorInterface $paginator
	 * @return Response
	 */
	public function index(Request $request, PaginatorInterface $paginator):Response
    {
    	$searchQuery    =   $request->get('query');
    	$repository     =   $this->getDoctrine()->getRepository(Post::class);
	    $posts          =   $repository->getActivityPosts();

    	if ($searchQuery !== null) {
		    $query = $repository->search($searchQuery);
	    } else {
		    $query = $repository->findAllPaginationQuery();
	    }

	    $pagination = $paginator->paginate(
		    $query, /* query NOT result */
		    $request->query->getInt('page', 1), /*page number*/
		    5 /*limit per page*/
	    );

	    $data = [
		    'pagination'    =>  $pagination,
		    'posts'         =>  $posts,
		    'searchQuery'   =>  $searchQuery,
	    ];

	    if ($request->isXmlHttpRequest()) {
		    return $this->render('default/parts/search.html.twig', $data);
	    }

        return $this->render('default/index.html.twig', $data);
    }

	/**
	 * @Route(
	 *     "/category/{category}/",
	 *     name="category",
	 *     requirements={
	 *          "category"="[A-zА-я0-9\%\-]+"
	 *     }
	 * )
	 * @param Request $request
	 * @param PaginatorInterface $paginator
	 * @return Response
	 */
	public function category(Request $request, PaginatorInterface $paginator):Response
    {
		$category       =   str_replace('_', ' ', $request->get('category'));
	    $searchQuery    =   $request->get('query');
	    $repository     =   $this->getDoctrine()->getRepository(Post::class);
	    $posts          =   $repository->getActivityPosts();

	    if ($searchQuery !== null) {
		    $query = $repository->searchWithCategory($searchQuery, $category);
	    } else {
		    $query = $repository->findByCategoryPagination($category);
	    }

	    $pagination = $paginator->paginate(
		    $query, /* query NOT result */
		    $request->query->getInt('page', 1), /*page number*/
		    5 /*limit per page*/
	    );

	    $data = [
		    'category'      =>  $category,
		    'pagination'    =>  $pagination,
		    'posts'         =>  $posts,
		    'searchQuery'   =>  $searchQuery
	    ];

	    if ($request->isXmlHttpRequest()) {
		    return $this->render('default/parts/search.html.twig', $data);
	    }

	    return $this->render('default/category.html.twig', $data);
    }
}
