-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               8.0.24 - MySQL Community Server - GPL
-- Операционная система:         Win64
-- HeidiSQL Версия:              11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Дамп данных таблицы finder.articles: ~11 rows (приблизительно)
DELETE FROM `articles`;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
INSERT INTO `articles` (`id`, `Title`, `Text`, `Link`) VALUES
	(1, 'Цивилизация 5', 'Цивилизация 5 считается одной из интересных игр современного ПК. Civilization - фаворит среди стратегий пошагового режима', '0'),
	(2, 'Civilization V', 'Чтобы установить игру, скачайте архив с инсталляцией Civilization.exe ', '0'),
	(3, 'Civ 5', 'Цивилизация Сид Мейера является классикой среди стратегий нашего времени', '0'),
	(4, 'Война на Украине', 'Войска РФ захватили Северодонецк и продвигаются в направлении Мариуполя', '0'),
	(5, 'Украина спецоперация', 'Российская артиллерия погасила передвижения вражеских войск на границе ЛНР и ДНР', '0'),
	(6, 'Стратегии нашего времени', 'Цивилизация 5 и Цивилизация 6 считается по праву наиболее востребованной игрой в наше время', '0'),
	(7, 'Скайрим прохождение', 'Прохождение скайрима от первой пещеры до победы над драконом', '0'),
	(8, 'Skyrim квест', 'Квест скайрима довольно легко пройти, если есть навыки магии разрушения и восстановления', '0'),
	(9, 'SQL Код', 'Запрос SELECT представляет собой обращение к базе данных и формирование определенной выборки данных согласно условиям запроса', '0'),
	(10, 'Select SQL', 'SQL - структурированный язык запросов, поэтому и команда SELECT выполняется непоследовательно, проходя через условия отбора данных и вывода их пользователю', '0'),
	(11, 'SQL Update', 'Команда Update предназначена для формирования обновленных данных, которые вводит пользователь. SQL четко регламентирует использование Update имя таблицы SET поле = значение ', '0');
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;

-- Дамп данных таблицы finder.keyquery: ~0 rows (приблизительно)
DELETE FROM `keyquery`;
/*!40000 ALTER TABLE `keyquery` DISABLE KEYS */;
/*!40000 ALTER TABLE `keyquery` ENABLE KEYS */;

-- Дамп данных таблицы finder.keyword: ~0 rows (приблизительно)
DELETE FROM `keyword`;
/*!40000 ALTER TABLE `keyword` DISABLE KEYS */;
/*!40000 ALTER TABLE `keyword` ENABLE KEYS */;

-- Дамп данных таблицы peoples.people: ~6 rows (приблизительно)
DELETE FROM `people`;
/*!40000 ALTER TABLE `people` DISABLE KEYS */;
INSERT INTO `people` (`id`, `fam`, `fname`, `sname`) VALUES
	(1, 'Петров', 'Иван', 'Васильевич'),
	(2, 'Иванов', 'Олег', 'Дмитриевич'),
	(3, 'Федоров', 'Андрей', 'Миронович'),
	(5, 'Сидоров', 'Алексей', 'Петрович'),
	(6, 'Романов1', 'Дмитрий2', 'Алексеевич3'),
	(14, '11111112', '2222223', '333334');
/*!40000 ALTER TABLE `people` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
