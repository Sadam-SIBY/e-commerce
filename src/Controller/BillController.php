<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BillController extends AbstractController
{
    /**
     * Route qui gère la facuture de la commande
     */
    #[Route('/editor/order/{id}/bill', name: 'app_bill')]
    public function index($id, OrderRepository $orderRepository): Response
    {

        $order = $orderRepository->find($id);
        $pdfOptions = new Options();
        //police par defaut
        $pdfOptions->set('defaultFont','Arial');
        $domPdf= new Dompdf($pdfOptions);

        // imprimer le rendu 

        $html = $this->renderView('bill/index.html.twig', [
            'order' => $order,
        ]);

        $domPdf->loadHtml($html);
        $domPdf->render();
        //nommination du fichier pdf
        $domPdf->stream("bill-".$order->getId().'.pdf', [
            //affichage du pdf
            'Attachment'=>false
        ]);



        return new Response( '', 200,[
            'Content-Type'=>'application/pdf'
        ]);
    }
}
