<?php
namespace  App\Controller;
use App\Entity\Picture;
use League\Csv\Reader;
use Exception;
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


            $em = $this->getDoctrine()->getManager();
            foreach ($results as $row) {

                //verify if the the row in a csv format and get out of the loop if not
                if ($this->NotValidField($row, $keys)) {
                    // It's not a valid row. Echo an error and skip to next row
                    var_dump("invalid row");
                    continue;
                }
                //verify if the content of the Image is valid and if it doesn't already exist, and get out of the loop if not
                $upload_result = $this->UploadPicture($row['Picture_url'], $row['Picture_title']);
                if ($upload_result == false) {
                    continue;
                }

                // Insert data to the database
                $picture = (new Picture())
                    ->setPictureTitle($row['Picture_title'])
                    ->setPictureUrl($row['Picture_url'])
                    ->setPictureDescription($row['Picture_description'])
                    ->setPicture($row['Picture_title']);
                $em->persist($picture);
            }$em->flush();


        }

    }

    // validate every field of a row
    public function NotValidField(array $data, array $keys)
    {
        foreach ($keys as $key) {
            // Make sure that the key exists, isn't null
            if (!isset($data[$key])) {
                return true;
            }
        }

        return false;
    }

    public function UploadPicture($url, $file_title)
    {

        //get rid of the spaces to creat the name of the local Image
        $file_title_strip = str_replace(' ', '', $file_title);
        //creat the link to the local directory that will be stored in the database fro every Picture
        $fileName = $this->getParameter('imgs_directory') . $file_title_strip . '.jpg';

        //validate the link
        try{
            $content = file_get_contents($url);
        }catch (Exception $e){
            var_dump("empty link");
            return false;
        }

        //validate the content of the Image
        if ($content!= null) {
            //creat or update
            file_put_contents($fileName, $content);

            //verify if update needed
            $update_result = $this->update_verif($file_title);
            // if no return true
            if ($update_result == false) {return true;}
            else{
                //if yes update database
                $entityManager = $this->getDoctrine()->getManager();
                $update_result->setPictureUrl($url);
                $entityManager->flush();
                var_dump("updated row");
                return false;
            }
        }
        else{
            return false;

        }

    }

    //verif if an update of Image is required
    public function Update_verif($file_title){

        $repository = $this->getDoctrine()->getRepository(Picture::class);
        // look for a single Image by title
        $Picture = $repository->findOneBy(["picture_title" => $file_title]);
        //var_dump(is_null ($Picture));
        if (is_null($Picture)) {
            //return false if the Image doesn't exist in the database
            return false;
        }
        //return an object if the image exist
        return $Picture;

    }





}
