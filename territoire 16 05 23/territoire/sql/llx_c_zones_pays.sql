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

CREATE TABLE `llx_c_zones_pays` (
  `rowid` int(11) NOT NULL,
  `fk_object` int(11) NOT NULL DEFAULT 0,
  `active` int(11) NOT NULL DEFAULT 1,
  `datec` datetime NOT NULL,
  `tms` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `entity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


ALTER TABLE `llx_c_zones_pays`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `fk_object` (`fk_object`);


ALTER TABLE `llx_c_zones_pays`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;




