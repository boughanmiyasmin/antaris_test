<?php
namespace  App\Controller;
use App\Entity\Picture;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;



/**
 *
 */
class UploadImagesAction extends AbstractController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request)
    {
        if ($request->isMethod('POST')) {
            // Pull the uploaded file information
            /** @var UploadedFile $file */
            $file = $request->files->get('file')->getRealPath();
            //read the content of a csv file and ignore the header
            $reader = Reader::createFromPath($file, 'r')->setOffset(1);
            // declare the keys
            $keys = ['Picture_title', 'Picture_url', 'Picture_description'];

            // delimiter is always a vertical bar
            $reader->setDelimiter('|');
            //Retrieve array
            $results = $reader->fetchAssoc($keys);

            // Insert data to the database
            $em = $this->getDoctrine()->getManager();
            foreach ($results as $row) {
                $picture = (new Picture())
                    ->setPictureTitle($row['Picture_title'])
                    ->setPictureUrl($row['Picture_url'])
                    ->setPictureDescription($row['Picture_description'])
                    ->setPicture($row['Picture_title']);
                $em->persist($picture);
            }$em->flush();


        }

    }



}
