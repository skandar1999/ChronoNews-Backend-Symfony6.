<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleRepository;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Titre = null;

    #[ORM\Column(length: 255)]
    private ?string $Contenu = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $publishDate = null;

    #[ORM\Column(length:255, nullable:true)]
    private ?string $image ;

    #[ORM\Column(length: 255, nullable:true)]
    private ?int $NbLikes = 0;

    #[ORM\Column(length: 255, nullable:true)]
    private ?int $NbDisLikes= 0; 

    #[ORM\Column(length: 255, nullable:true)]
    private ?int $NbComments= 0;

    #[ORM\Column(length: 255)]
    private ?string $Catégorie = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comment::class)]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(string $Titre): self
    {
        $this->Titre = $Titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->Contenu;
    }

    public function setContenu(string $Contenu): self
    {
        $this->Contenu = $Contenu;

        return $this;
    }

    public function getPublishDate(): ?DateTimeInterface
    {
        return $this->publishDate;
    }
    
    public function setPublishDate(DateTimeInterface $date): self
    {
        $this->publishDate = $date;
    
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
    
    public function setImage(?string $image): self
    {
        $this->image = $image;
    
        return $this;
    }

    public function getNbLikes(): ?string
    {
        return $this->NbLikes;
    }

    public function setNbLikes(string $NbLikes): self
    {
        $this->NbLikes = $NbLikes;

        return $this;
    }

    public function getNbDisLikes(): ?string
    {
        return $this->NbDisLikes;
    }

    public function setNbDisLikes(string $NbDisLikes): self
    {
        $this->NbDisLikes = $NbDisLikes;

        return $this;
    }

    public function getNbComments(): ?string
    {
        return $this->NbComments;
    }

    public function setNbComments(string $NbComments): self
    {
        $this->NbComments = $NbComments;

        return $this;
    }

    public function getCatégorie(): ?string
    {
        return $this->Catégorie;
    }

    public function setCatégorie(string $Catégorie): self
    {
        $this->Catégorie = $Catégorie;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }
}
