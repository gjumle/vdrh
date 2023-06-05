CREATE TABLE `hry` (
  `ID_hry` int NOT NULL,
  `nazev` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_czech_ci NOT NULL,
  `datum_vydani` date NOT NULL,
  `popis` varchar(800) CHARACTER SET utf8mb3 COLLATE utf8mb3_czech_ci DEFAULT NULL,
  `ID_vydavatel` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

CREATE TABLE `hry_platformy` (
  `ID_hry` int NOT NULL,
  `ID_platformy` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

CREATE TABLE `hry_zanry` (
  `ID_hry` int NOT NULL,
  `ID_zanr` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

CREATE TABLE `platformy` (
  `ID_platformy` int NOT NULL,
  `nazev_platformy` varchar(30) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_czech_ci;

CREATE TABLE `reviews` (
  `ID_rev` int NOT NULL,
  `review` varchar(600) CHARACTER SET utf8mb3 COLLATE utf8mb3_czech_ci DEFAULT NULL,
  `rating` tinyint NOT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ID_hrac` int NOT NULL,
  `ID_hry` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_czech_ci;

CREATE TABLE `uzivatele` (
  `ID_hrac` int NOT NULL,
  `nickname` varchar(100) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL,
  `user_type` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_czech_ci;

CREATE TABLE `vydavatel` (
  `ID_vydavatel` int NOT NULL,
  `nazev_vydavatel` varchar(30) CHARACTER SET utf16 COLLATE utf16_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf16 COLLATE=utf16_czech_ci;


CREATE TABLE `zanr` (
  `ID_zanr` int NOT NULL,
  `nazev_zanr` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_czech_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;


ALTER TABLE `hry_platformy`
  ADD PRIMARY KEY (`ID_hry`,`ID_platformy`),
  ADD KEY `ID_platformy` (`ID_platformy`);

ALTER TABLE `hry`
  ADD PRIMARY KEY (`ID_hry`),
  ADD UNIQUE KEY `nazev` (`nazev`),
  ADD KEY `ID_vydavatel` (`ID_vydavatel`);

ALTER TABLE `hry_zanry`
  ADD PRIMARY KEY (`ID_hry`,`ID_zanr`),
  ADD KEY `ID_zanr` (`ID_zanr`);

ALTER TABLE `platformy`
  ADD PRIMARY KEY (`ID_platformy`),
  ADD UNIQUE KEY `nazev_platformy` (`nazev_platformy`);

ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ID_rev`),
  ADD KEY `ID_hrac` (`ID_hrac`),
  ADD KEY `ID_hry` (`ID_hry`);

ALTER TABLE `uzivatele`
  ADD PRIMARY KEY (`ID_hrac`),
  ADD UNIQUE KEY `nickname` (`nickname`),
  ADD UNIQUE KEY `email` (`email`);


ALTER TABLE `vydavatel`
  ADD PRIMARY KEY (`ID_vydavatel`),
  ADD UNIQUE KEY `nazev_vydavatel` (`nazev_vydavatel`);

ALTER TABLE `zanr`
  ADD PRIMARY KEY (`ID_zanr`),
  ADD UNIQUE KEY `nazev_zanr` (`nazev_zanr`);

ALTER TABLE `hry`
  MODIFY `ID_hry` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

ALTER TABLE `platformy`
  MODIFY `ID_platformy` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

ALTER TABLE `uzivatele`
  MODIFY `ID_hrac` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;


ALTER TABLE `reviews`
  MODIFY `ID_rev` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

ALTER TABLE `vydavatel`
  MODIFY `ID_vydavatel` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

ALTER TABLE `zanr`
  MODIFY `ID_zanr` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;