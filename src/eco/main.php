<?php
namespace eco;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\utils\Config;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class main extends PluginBase implements Listener {

	public function onEnable(){
		$this->eco = new Config('data/Economy/data.json', Config::JSON);

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onJoin(PlayerJoinEvent $e){
		$p = $e->getPlayer();
		if (!$this->eco->exists(mb_strtolower($e->getPlayer()->getName()))){
			$this->eco->setNested(mb_strtolower($p->getName()) . ".rubs", 0);
			$this->eco->save();
		}
	}

	public function getMoney($nick){
		$nick = mb_strtolower($nick);
		return $this->eco->getNested($nick . '.money');
	}

	public function getRubs($nick){
		$nick = mb_strtolower($nick);
		return $this->eco->getNested($nick.'.rubs');
	}

	public function addRubs($nick, $c){
		$nick = mb_strtolower($nick);
		$r = $this->getRubs($nick);
		$c = $c + $r;
		$this->eco->setNested($nick.'.rubs', $c);
		$this->eco->save();
	}

	public function addMoney($nick, $c){
		$nick = mb_strtolower($nick);
		$m = $this->getMoney($nick);
		$c = $c + $m;
		$this->eco->setNested($nick.'.money', $c);
		$this->eco->save();
	}

	public function rmMoney($nick, $c){
		$nick = mb_strtolower($nick);
		$m = $this->getMoney($nick);
		if ($m < $c){
			return false;
		}
		$ce = $m - $c;
		$this->eco->setNested($nick .'.money', $ce);
		$this->eco->save();
	}

	public function rmRubs($nick, $c){
		$nick = mb_strtolower($nick);
		$m = $this->getRubs($nick);
		if ($m < $c){
			return false;
		}
		$ce = $m-$c;
		$this->eco->setNested($nick .'.rubs', $ce);
		$this->eco->save();
	}

	public function onCommand(CommandSender $s, Command $cmd, $label, array $args){
		$cmd = mb_strtolower($cmd->getName());
		$sender = $s;
		$nick = $s->getName();
		if ($cmd == 'money') return $sender->sendMessage('§l§bEdit§aMC§r §fваш баланс: §e' .$this->getMoney($nick).'§c монет.');
		if ($cmd == 'rubs') return $sender->sendMessage('§l§bEdit§aMC§r §fваш баланс: §e' .$this->getRubs($nick).'§c рублей.');

		if ($cmd == 'givemoney'){
			if (!$sender->hasPermission('adm')) return $sender->sendMessage('§l§bEdit§aMC§r §cНЕТ ПРАВ');
			if (!isset($args[1])) return $sender->sendMessage('§l§bEdit§aMC§r §fИспользование: §e/givemoney §7(§cигрок§7)§7 (§cколичество§7)');
			if (!$this->eco->exists($args[0])) return $sender->sendMessage('§l§bEdit§aMC§r §cтакого игрока нет');
			if (!is_numeric($args[1])) return $sender->sendMessage('§l§bEdit§aMC§r §eвторой аргумент должен быть числом');

			$this->addMoney($args[0], $args[1]);
			$s->sendMessage('§l§bEdit§aMC§r §eВы успешно выдали игроку §f'.$args[0].' '.$args[1].'§c монет.');
		}

		if ($cmd == 'giverubs'){
			if (!$sender->hasPermission('adm')) return $sender->sendMessage('§l§bEdit§aMC§r §cНЕТ ПРАВ');
			if (!isset($args[1])) return $sender->sendMessage('§l§bEdit§aMC§r §fИспользование: §e/giverubs §7(§cигрок§7)§7 (§cколичество§7)');
			if (!$this->eco->exists($args[0])) return $sender->sendMessage('§l§bEdit§aMC§r §cтакого игрока нет');
			if (!is_numeric($args[1])) return $sender->sendMessage('§l§bEdit§aMC§r §eвторой аргумент должен быть числом');

			$this->addRubs($args[0], $args[1]);
			$s->sendMessage('§l§bEdit§aMC§r §eВы успешно выдали игроку §f'.$args[0].' '.$args[1].'§c рублей.');
		}
	}

}
?>