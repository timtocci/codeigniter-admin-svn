-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июл 28 2011 г., 22:24
-- Версия сервера: 5.1.49
-- Версия PHP: 5.3.3-1ubuntu9.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `Alexey`
--

-- --------------------------------------------------------

--
-- Структура таблицы `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instructions` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `config`
--

INSERT INTO `config` (`id`, `instructions`) VALUES
(1, '');

-- --------------------------------------------------------

--
-- Структура таблицы `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `images`
--

INSERT INTO `images` (`id`, `file`) VALUES
(1, '32287f1b.png'),
(2, 'b44ca70c3b02.png'),
(3, '118552439.png'),
(4, 'ac7988f.png');

-- --------------------------------------------------------

--
-- Структура таблицы `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `panorama_id` int(11) NOT NULL,
  `panorama_id_to_link` int(11) NOT NULL,
  `title` text NOT NULL,
  `heading` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

--
-- Дамп данных таблицы `links`
--

INSERT INTO `links` (`id`, `panorama_id`, `panorama_id_to_link`, `title`, `heading`) VALUES
(34, 47, 46, 'Lable Import link1', 92),
(33, 46, 45, 'Lable Import link2', 167),
(32, 46, 38, 'Lable Import link1', 80),
(31, 45, 48, 'Lable Import link2', 167),
(30, 45, 23, 'Lable Import link1', 14.5),
(29, 43, 42, 'Lable Import link1', 92),
(26, 41, 44, 'Lable Import link2', 167),
(25, 41, 23, 'Lable Import link1 Edit ttt', 14.5),
(24, 39, 38, 'Lable Import link1', 92),
(23, 38, 37, 'Lable Import link2', 167),
(22, 38, 38, 'Lable Import link1', 80),
(21, 37, 40, 'Lable Import link2', 167),
(20, 37, 23, 'Lable Import link1 Edit', 140.5);

-- --------------------------------------------------------

--
-- Структура таблицы `maps_overlays`
--

CREATE TABLE IF NOT EXISTS `maps_overlays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` text NOT NULL,
  `line_color` varchar(6) NOT NULL DEFAULT '',
  `fill_color` varchar(6) NOT NULL DEFAULT '',
  `info` text NOT NULL,
  `object_type` int(11) NOT NULL,
  `link_name` text NOT NULL,
  `width` int(3) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Дамп данных таблицы `maps_overlays`
--

INSERT INTO `maps_overlays` (`id`, `link`, `line_color`, `fill_color`, `info`, `object_type`, `link_name`, `width`) VALUES
(11, 'http:\\\\\\\\', 'ba22ba', '0e90b8', 'ccsdsd', 2, '', 2),
(10, 'http:\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\', '20c75a', '49cc29', 'sams', 1, '', 10),
(14, 'http://', 'b82cb8', 'c219c2', 'In this orrswdwdwd', 2, 'ssssssssssss', 20),
(12, 'http://asss', 'b82eb8', '2b1b2b', '', 2, '', 2),
(15, 'http://', '', '', '', 1, 'Link', 2),
(16, 'http://', '', '', '', 1, 'Link', 2),
(17, 'http:\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\', '20c75a', '49cc29', 'sams', 2, '', 10);

-- --------------------------------------------------------

--
-- Структура таблицы `markers`
--

CREATE TABLE IF NOT EXISTS `markers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `panorama_id` int(11) NOT NULL,
  `name` text CHARACTER SET utf8 NOT NULL,
  `link` text CHARACTER SET utf8 NOT NULL,
  `image` int(11) NOT NULL,
  `x_gradus` float NOT NULL,
  `y_gradus` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Дамп данных таблицы `markers`
--

INSERT INTO `markers` (`id`, `panorama_id`, `name`, `link`, `image`, `x_gradus`, `y_gradus`) VALUES
(18, 34, 'Magazin', 'http://google.com', 3, 276.12, 40.5),
(16, 5, 'This is</br> marker name', '2222', 4, 130.32, 48.15),
(17, 34, 'Magazin', 'http://google.com', 3, 104.4, 12.15),
(14, 5, 'nww', '2323', 2, 11.88, 36.675);

-- --------------------------------------------------------

--
-- Структура таблицы `overlays_items`
--

CREATE TABLE IF NOT EXISTS `overlays_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `overlay_id` int(11) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=130 ;

--
-- Дамп данных таблицы `overlays_items`
--

INSERT INTO `overlays_items` (`id`, `overlay_id`, `lat`, `lng`) VALUES
(107, 16, 6.28160040671468, -75.5251691901856),
(106, 16, 6.26317196017241, -75.5158994758301),
(105, 16, 6.25327493322984, -75.545425232666),
(104, 16, 6.26829103843646, -75.5516050422364),
(103, 15, 0, 6.30036945011154),
(102, 15, 0, 6.26419577984221),
(101, 15, 0, 6.25566388793033),
(100, 15, 0, 6.27614019418997),
(129, 17, 6.17409199829395, -75.4075811469727),
(128, 17, 6.15702515467586, -75.4707525336914),
(127, 17, 6.20651748487999, -75.4470632636719),
(126, 10, 6.20088583274854, -75.5701444709474),
(125, 10, 6.19849662882277, -75.4977033698731),
(124, 10, 6.22204688023613, -75.4705808723145),
(122, 14, 6.21880451687305, -75.6805227363282),
(121, 14, 6.20788272481813, -75.651683625),
(120, 14, 6.22563052173188, -75.6345174873047),
(119, 14, 6.2659021414958, -75.6554601752929),
(123, 10, 6.25071532679609, -75.4990766608886),
(35, 11, 6.17323866916139, -75.5946920478516),
(36, 11, 6.14866220100524, -75.5631063544922),
(37, 11, 6.12476732365707, -75.6091116035157),
(38, 11, 6.15275835802047, -75.6338308417969),
(79, 12, 6.26777913287003, -75.7349393928223),
(78, 12, 6.23842904166196, -75.7260130012207),
(77, 12, 6.26504896136637, -75.695800598877),
(118, 14, 6.24542543381662, -75.6825826728516);

-- --------------------------------------------------------

--
-- Структура таблицы `panorams`
--

CREATE TABLE IF NOT EXISTS `panorams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` text NOT NULL,
  `name` text NOT NULL,
  `adress` text NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `heading` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49 ;

--
-- Дамп данных таблицы `panorams`
--

INSERT INTO `panorams` (`id`, `file`, `name`, `adress`, `lat`, `lng`, `heading`) VALUES
(8, '52c8301.jpeg', 'Pano4 Import', '', 0, 0, 0),
(48, 'abfad00d4308.jpeg', 'Pano4 Import New', 'Pan Adr4', 0, 0, 89.1),
(47, 'f4b8a84.jpeg', 'Pano3 Import New', 'Pan Adr3', 0, 0, 25),
(46, '98b1697.jpeg', 'Pano2 Import New', 'Pan Adr2', 0, 0, 78.5),
(45, '3e135d3.jpeg', 'Pano1 Import New', 'Pan Adr1', 0, 0, 278),
(44, '7519f8289a453.jpeg', 'Pano4 Import New', 'Pan Adr4', 0, 0, 89.1),
(43, '86b4d894.jpeg', 'Pano3 Import New', 'Pan Adr3', 0, 0, 25),
(42, '580617199.jpeg', 'Pano2 Import New', 'Pan Adr2', 0, 0, 78.5),
(41, 'bae5d86b.jpeg', 'Pano1 Import New', 'Pan Adr1', 0, 0, 278),
(40, 'ceac10f5c.jpeg', 'Pano4 Import New', 'Pan Adr4', 0, 0, 89.1),
(39, '31bf779682.jpeg', 'Pano3 Import New', 'Pan Adr3', 0, 0, 25),
(38, '8a81d7ea.jpeg', 'Pano2 Import New', 'Pan Adr2', 0, 0, 78.5),
(37, '2bbaa451.jpeg', 'Pano1 Import New', 'Pan Adr1', 0, 0, 278),
(36, 'ca4c8ba4385e2.jpeg', 'Pano4 Import New', 'Pan Adr4', 0, 0, 89.1),
(35, 'd681aca95.jpeg', 'Pano3 Import New', 'Pan Adr3', 0, 0, 25),
(34, '1ff994f.jpeg', 'Pano2 Import New Denis', 'Pan Adr2', 0, 0, 67.68),
(33, '3f7b11d53.jpeg', 'Pano1 Import New', 'Pan Adr1', 0, 0, 278.22);
