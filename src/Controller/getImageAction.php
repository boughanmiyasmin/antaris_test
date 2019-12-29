<?php
namespace  App\Controller;
use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
/**
 *
 */
class getImageAction extends AbstractController
{
    //the id is given as Parameter
    /**
     * @param Request $request
     * @param $id
     *
     */
    public function __invoke(Request $request, $id)
    {
        // get the line by $id from database
        $repository = $this->getDoctrine()->getRepository(Picture::class);
        $file = $repository->find($id);
        //return the Image itself using the local link stored in field Picture
        //disable Range and Content-Length handling because the size of the image is unknown
        $stream  = new Stream("../".$file->getPicture());
        $response = new BinaryFileResponse($stream);
        //return the Image
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->send();
    }
}