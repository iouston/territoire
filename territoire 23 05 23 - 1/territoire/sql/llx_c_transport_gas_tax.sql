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

CREATE TABLE `llx_c_transport_gas_tax` (
  `rowid` int(11) NOT NULL,
  `fk_transporteur` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `gastax` decimal(4,2) NOT NULL,
  `entity` int(11) NOT NULL,
  `tms` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


ALTER TABLE `llx_c_transport_gas_tax`
  ADD PRIMARY KEY (`rowid`,`fk_transporteur`),
  ADD KEY `fk_transporteur` (`fk_transporteur`);


ALTER TABLE `llx_c_transport_gas_tax`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;



