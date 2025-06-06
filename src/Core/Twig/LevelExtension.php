<?php

namespace Citadel\Levels\Core\Twig;

use Citadel\Levels\Core\Entity\Level;
use Citadel\Levels\Core\Repository\LevelRepository;
use Citadel\Levels\Core\Repository\UserXpRepository;
use Forumify\Core\Entity\User;
use Forumify\Core\Repository\SettingRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LevelExtension extends AbstractExtension
{
    public function __construct(
        private readonly LevelRepository $levels,
        private readonly UserXpRepository $userXpRepository,
        private readonly SettingRepository $settingRepository,
    ){}

    public function getFunctions()
    {
        return [
            new TwigFunction('levelConfig', [$this, 'getLevelConfig']),
            new TwigFunction('level', [$this, 'getUserLevel']),
            new TwigFunction('xp', [$this, 'getUserXp']),
        ];
    }
    public function getLevelConfig(): bool
    {
        $threadXp = $this->settingRepository->get('levels.thread_post_xp');
        $postXp = $this->settingRepository->get('levels.comment_post_xp');
        return $threadXp !== null && $postXp !== null;
    }
    public function getUserLevel(User $user): ?Level
    {
        $xp = $this->getUserXp($user);
        return $this->levels->findByXp($xp);
    }

    public function getUserXp(User $user): int
    {
        $userXp = $this->userXpRepository->findOneBy(['user' => $user]);
        return $userXp?->getXp() ?? 0;
    }
}