<?php

declare(strict_types=1);

namespace App\Http\Main\Form\Configuration;

use App\Domain\Guild\Entity\GuildSettings;
use App\Infrastructure\Discord\Entity\Channel\AbstractDiscordChannel;
use App\Infrastructure\Discord\Entity\Channel\CategoryChannel;
use App\Infrastructure\Discord\Entity\Channel\ICategoryChannelParent;
use App\Infrastructure\Discord\Entity\Channel\TextChannel;
use App\Infrastructure\Discord\IDiscordGuildService;
use App\Infrastructure\Parameter\IParameterService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuildSettingsType extends AbstractType
{
    public function __construct(
        private IParameterService $parameterService,
        private IDiscordGuildService $guildService
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('workAnnounceChannelId', ChoiceType::class, [
                'choice_loader' => new CallbackChoiceLoader(fn () => $this->_formatChannel($this->_getChannels())),
                'choice_translation_domain' => false,
                'translation_domain' => false,
                'label' => "Salon d'annonce des devoirs",
                'attr' => ['class' => 'ts-select'],
            ])
            ->add('workRecallChannelId', ChoiceType::class, [
                'choice_loader' => new CallbackChoiceLoader(fn () => $this->_formatChannel($this->_getChannels())),
                'choice_translation_domain' => false,
                'translation_domain' => false,
                'label' => 'Salon de rappel des devoirs',
                'attr' => ['class' => 'ts-select'],
            ])
            ->add('announceChannelId', ChoiceType::class, [
                'choices' => $this->_formatChannel($this->guildService->getChannels((int) $this->parameterService->getGuildId())),
                'choice_translation_domain' => false,
                'translation_domain' => false,
                'disabled' => true,
                'label' => "Catégorie du channel d'annonce du serveur",
                'attr' => ['class' => 'ts-select'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Sauvegarder',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GuildSettings::class,
        ]);
    }

    public function _formatChannel(array $channels): array
    {
        return array_reduce($channels, function (array $acc, AbstractDiscordChannel $channel) {
            $acc[$this->_getChannelLabel($channel)] = (string) ($channel->getId());

            return $acc;
        }, []);
    }

    public function _getChannelLabel(AbstractDiscordChannel $c): string
    {
        $str = '';
        if ($c instanceof ICategoryChannelParent) {
            $p = $c->getParent();
            if ($p !== null) {
                $str .= $p->getName() . ' | ';
            }
        }

        return $str . $c->getName();
    }

    public function getBlockPrefix(): string
    {
        return 'guild_settings';
    }

    private function _getChannels(): array
    {
        $data = array_reduce($this->guildService->getChannels((int) $this->parameterService->getGuildId()), function (array $acc, AbstractDiscordChannel $channel) {
            if ($channel instanceof TextChannel) {
                $acc[] = $channel;
            } elseif ($channel instanceof CategoryChannel) {
                $acc = array_merge($acc, $channel->getChannels()->filter(fn (AbstractDiscordChannel $c) => $c instanceof TextChannel)->toArray());
            }

            return $acc;
        }, []);

        usort($data, function (AbstractDiscordChannel $a, AbstractDiscordChannel $b) {
            if ($a instanceof ICategoryChannelParent && $b instanceof ICategoryChannelParent && $a->getParent() !== $b->getParent()) {
                $parent_a = $a->getParent();
                $parent_b = $b->getParent();
                if ($parent_a !== null && $parent_b !== null && $parent_a->getPosition() !== $parent_b->getPosition()) {
                    return $parent_a->getPosition() > $parent_b->getPosition() ? 1 : -1;
                }
            }

            return $a->getPosition() > $b->getPosition() ? 1 : ($a->getPosition() === $b->getPosition() ? 0 : -1);
        });

        return $data;
    }
}
