-- ============================================================================
-- Copyright (C) 2017 Mikael Carlavan  <contact@mika-carl.fr>
-- Copyright (C) 2022 Julien Marchand <julien.marchand@iouston.com>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
-- ============================================================================

CREATE TABLE `llx_c_tranches_poids_departements` (
  `rowid` int(11) NOT NULL,
  `fk_transporteur` int(11) NOT NULL DEFAULT 0,
  `fk_zone` int(11) NOT NULL DEFAULT 0,
  `poids_min` double(24,8) NOT NULL DEFAULT 0.00000000,
  `poids_max` double(24,8) NOT NULL DEFAULT 0.00000000,
  `fk_unit_min` int(11) NOT NULL DEFAULT 0,
  `fk_unit_max` int(11) NOT NULL DEFAULT 0,
  `montant` double(24,8) NOT NULL DEFAULT 0.00000000,
  `datec` datetime NOT NULL,
  `tms` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(11) NOT NULL DEFAULT 1,
  `entity` int(11) NOT NULL DEFAULT 1,
  `import_key` varchar(14) DEFAULT NULL,
  `poids_reel` int(11) NOT NULL DEFAULT 1,
  `roundsup10` tinyint(1) DEFAULT 0,
  `div100` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `llx_c_tranches_poids_departements`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `fk_transporteur` (`fk_transporteur`),
  ADD KEY `fk_zone` (`fk_zone`),
  ADD KEY `fk_unit_min` (`fk_unit_min`),
  ADD KEY `fk_unit_max` (`fk_unit_max`);

ALTER TABLE `llx_c_tranches_poids_departements`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;



