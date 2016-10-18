<?php

namespace ISC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * ActiviteImage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ISC\PlatformBundle\Entity\ActiviteImageRepository")
 */
class ActiviteImage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="urlImage", type="string", length=255, nullable=true)
     */
    private $urlImage;

    private $file;

    private $editFile;

    /**
     * @ORM\Column(name="imageName", type="text", nullable=true)
     */
    private $imageName;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    public function upload()
    {
        // On récupère le nom original du fichier de l'internaute
        $name = md5(uniqid()).'.'.$this->$file->guessExtension();

        // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move($this->getUploadRootDir(), $name);

        // On sauvegarde le nom de fichier dans notre attribut $url
        $this->url = $name;

        return $name;
    }

    public function getUploadDir()
    {
        // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
        return 'uploads/img';
    }

    protected function getUploadRootDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return $this->get('kernel')->getRootDir().'/../web/uploads/img/'.$this->getUploadDir();
    }



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set urlImage
     *
     * @param string $urlImage
     * @return ActiviteImage
     */
    public function setUrlImage($urlImage)
    {
        $this->urlImage = $urlImage;

        return $this;
    }

    /**
     * Get urlImage
     *
     * @return string 
     */
    public function getUrlImage()
    {
        return $this->urlImage;
    }

    /**
     * Set editFile
     *
     * @param boolean $editFile
     * @return UserAvatar
     */
    public function setEditFile($editFile)
    {
        $this->editFile = $editFile;

        return $this;
    }

    /**
     * Get editFile
     *
     * @return boolean
     */
    public function getEditFile()
    {
        return $this->editFile;
    }

    /**
     * Set imageName
     *
     * @param integer $imageName
     * @return ActiviteImage
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName
     *
     * @return integer 
     */
    public function getImageName()
    {
        return $this->imageName;
    }
}
