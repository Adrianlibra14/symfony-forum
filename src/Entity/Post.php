<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $body;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="post", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=Dislike::class, mappedBy="post", orphanRemoval=true)
     */
    private $dislikes;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->dislikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    public function likedBy(User $user)
    {
        // Check if the post has been already liked by the user
        // forAll() tests whether the given predicate holds for all elements of the collection.
        $liked = !$this->likes->forAll(function($key, $value) use ($user){
            return $value->getUser()->getId() !== $user->getId();
        });

        return $liked;
    }

    /**
     * @return Collection|Dislike[]
     */
    public function getDislikes(): Collection
    {
        return $this->dislikes;
    }

    public function addDislike(Dislike $dislike): self
    {
        if (!$this->dislikes->contains($dislike)) {
            $this->dislikes[] = $dislike;
            $dislike->setPost($this);
        }

        return $this;
    }

    public function removeDislike(Dislike $dislike): self
    {
        if ($this->dislikes->removeElement($dislike)) {
            // set the owning side to null (unless already changed)
            if ($dislike->getPost() === $this) {
                $dislike->setPost(null);
            }
        }

        return $this;
    }

    public function dislikedBy(User $user)
    {
        // Check if the post has been already disliked by the user
        // forAll() tests whether the given predicate holds for all elements of the collection.
        $disliked = !$this->dislikes->forAll(function($key, $value) use ($user){
            return $value->getUser()->getId() !== $user->getId();
        });

        return $disliked;
    }
}
