<?php


namespace Prenomea\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Prenomea\SiteBundle\Entity\Prenom;
use Prenomea\SiteBundle\Entity\Etymology;
use Ob\HighchartsBundle\Highcharts\Highchart;

class SiteController extends Controller
{
  public function accueilAction($id_etymology,$id_prenom)
  {
    return $this->render('PrenomeaSiteBundle:Site:accueil.html.twig', array(
      'id_prenom' => $id_prenom , 'id_etymology' => $id_etymology 
      ));
  }

  public function afficherAction($id_etymology,$id_prenom)
  {
    if($id_etymology!=0)
    {
      $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('PrenomeaSiteBundle:Etymology');

      $etymology = $repository->find($id_etymology);
      if($etymology === null)
      {
        throw $this->createNotFoundException('Etymology[id='.$id_etymology.'] inexistant.');
      }
      $ans = $etymology->getName();
    }
    else if($id_prenom!=0)
    {
      $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('PrenomeaSiteBundle:Prenom');

      $prenom = $repository->find($id_prenom);
      $ans = $prenom->getPrenom();
      if($prenom === null)
      {
        throw $this->createNotFoundException('Prenom[id='.$id_prenom.'] inexistant.');
      }
    }
    else
    {
      $ans ='Recherchez un prénom !';
    }
    
    return $this->render('PrenomeaSiteBundle:Site:afficher.html.twig', array(
      'prenom' => $ans
      ));
  }


  public function etymologyAction($id_etymology)
  {
    if($id_etymology!=0)
    {
      $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('PrenomeaSiteBundle:Etymology');

      $etymology = $repository->find($id_etymology);
      if($etymology === null)
      {
        throw $this->createNotFoundException('Etymology[id='.$id_etymology.'] inexistant.');
      }
    }
    else
    {
      $etymology = new Etymology;
    }
    return $this->render('PrenomeaSiteBundle:Site:etymology.html.twig', array(
      'etymology' => $etymology
      ));
  }

  public function formulaireAction()
  {
    $prenom = new Prenom;

    $form = $this->createFormBuilder($prenom)
    ->setAction($this->generateUrl('PrenomeaSite_formulaire'))
    ->setMethod('POST')
    ->add('prenom','text')
    // ->add('sexe', 'choice', array(
    //   'choices'   => array('M' => 'Masculin', 'F' => 'Féminin'),
    //   'expanded'  => true,
    //   'multiple'  => false,
    //   'empty_data'  => 'M'))

    ->add('Rechercher','submit')
    ->getForm();


    $request = $this->get('request');
    // On vérifie qu'elle est de type POST
    if ($request->getMethod() == 'POST') 
    {

      $form->bind($request);  
      if ($form->isValid()) 
      {
        $repository = $this->getDoctrine()
        ->getManager()
        ->getRepository('PrenomeaSiteBundle:Prenom');

        $listePrenom = $repository->findBy(array('prenom' => $prenom->getPrenom()),
         array(),
         1,
         0);

        $repository = $this->getDoctrine()
        ->getManager()
        ->getRepository('PrenomeaSiteBundle:Etymology');

        $listeEtymology = $repository->findBy(array('name' => $prenom->getPrenom()),
         array(),
         1,
         0);

        if($listePrenom==null)
        {
          $listePrenom = array (new Prenom());
        }
        if($listeEtymology==null)
        {
          $listeEtymology = array (new Etymology());
        }
        
        return $this->redirect( $this->generateUrl('PrenomeaSite_accueil', array('id_etymology' =>$listeEtymology[0]->getId(), 'id_prenom'=>$listePrenom[0]->getId())));
      }

    }

    // À ce stade :
    // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
    // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau

    return $this->render('PrenomeaSiteBundle:Site:formulaire.html.twig', array(
      'form' => $form->createView(),
      ));
  }



  public function chartAction($id_prenom)
  {
    if($id_prenom!=0)
    {
       $repository = $this->getDoctrine()
       ->getManager()
       ->getRepository('PrenomeaSiteBundle:Prenom');


       $prenom = $repository->find($id_prenom);
       $listePrenom = $repository->findBy(array('prenom' => $prenom->getPrenom(),'sexe' => $prenom->getSexe()),
         array('anne' => 'asc'),
         5,
         0);

       $data = array();
       $categories = array();

       foreach($listePrenom as $prenom)
       {
          array_push($categories,$prenom->getAnne());
          array_push($data,$prenom->getNombre());
       }
          // Chart
       $series = array(
        array("type" => "column","name" => "Naissance" , "data" => $data)
        );

       $ob = new Highchart();
          $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart
          $ob->title->text('');
          $ob->yAxis->title(array('text'  => ""));
          $ob->xAxis->type('dateTime');
          $ob->series($series);
          $ob->legend->borderColor('rgba(244, 184, 68, .9)');
          $ob->legend->enabled(false);
          $ob->xAxis->categories($categories);
          $ob->chart->plotBackgroundColor('rgba(244, 184, 68, .9)');
          $ob->chart->backgroundColor('rgba(244, 184, 68, .9)');
          $ob->yAxis->gridLineColor('rgba(255, 255, 255, .9)');
          // $ob->navigation->buttonOptions->enabled(false);

      }

      else 
      {
        $ob = new HighChart();
      }
      return $this->render('PrenomeaSiteBundle:Site:chart.html.twig', array(
                'chart' => $ob
        ));
    }



  }

  ?>