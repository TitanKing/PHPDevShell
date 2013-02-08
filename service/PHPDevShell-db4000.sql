/*[1091]*/DROP INDEX `index` ON `pds_core_menu_items`;
ALTER TABLE `pds_core_menu_items` modify `parent_menu_id` VARCHAR(64);
/*[1061,1071]*/CREATE INDEX `index`  ON `pds_core_menu_items`(`parent_menu_id`, `menu_link`, `plugin`, `alias`);
