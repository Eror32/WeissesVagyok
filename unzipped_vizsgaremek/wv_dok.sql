-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Feb 17. 08:43
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `wv`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `dok`
--

CREATE TABLE `dok` (
  `did` int(11) NOT NULL,
  `dpostid` int(11) NOT NULL,
  `dtextid` int(11) NOT NULL,
  `duid` int(11) NOT NULL,
  `dtext` text NOT NULL,
  `dfile` text DEFAULT NULL,
  `dvote` text NOT NULL,
  `devent` varchar(1) NOT NULL,
  `deventEnd` date DEFAULT NULL,
  `dtime` datetime NOT NULL,
  `dstatus` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `votelog`
--

CREATE TABLE `votelog` (
  `vid` int(11) NOT NULL,
  `vdpostid` int(11) NOT NULL,
  `vuid` int(11) NOT NULL,
  `vchoice` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `dok`
--
ALTER TABLE `dok`
  ADD PRIMARY KEY (`did`);

--
-- A tábla indexei `votelog`
--
ALTER TABLE `votelog`
  ADD PRIMARY KEY (`vid`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `dok`
--
ALTER TABLE `dok`
  MODIFY `did` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `votelog`
--
ALTER TABLE `votelog`
  MODIFY `vid` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
