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

CREATE TABLE `llx_c_transporteurs` (
  `rowid` int(11) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `ref` varchar(255) NOT NULL,
  `datec` datetime NOT NULL,
  `fk_product` int(11) NOT NULL DEFAULT 0,
  `out_of_range` int(11) NOT NULL DEFAULT 0,
  `subject_to_fuel_tax` int(11) NOT NULL,
  `tms` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `entity` int(11) NOT NULL DEFAULT 1,
  `active` int(11) NOT NULL,
  `fk_shipping_method` int(11) NOT NULL DEFAULT 0
  `usesaison` tinyint(1) DEFAULT NULL,
  `tracking_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `llx_c_transporteurs`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `fk_soc` (`fk_soc`),
  ADD KEY `fk_product` (`fk_product`),
  ADD KEY `fk_shipping_method` (`fk_shipping_method`);

ALTER TABLE `llx_c_transporteurs`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;


