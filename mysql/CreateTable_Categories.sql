CREATE TABLE `OMalleyLandBudget`.`Categories` (
  `ID` INTEGER  NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR  NOT NULL,
  PRIMARY KEY (`ID`, `Name`)
)
ENGINE = MyISAM
AUTO_INCREMENT = 1
COMMENT = 'Budget Categories';
