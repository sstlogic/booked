# noinspection SqlNoDataSourceInspectionForFile
CREATE TABLE `resource_relationships`
(
    `resource_relationship_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    `resource_id`              SMALLINT UNSIGNED NOT NULL,
    `related_resource_id`      SMALLINT UNSIGNED NOT NULL,
    `relationship_type`        TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (`resource_relationship_id`),
    FOREIGN KEY (`resource_id`)
        REFERENCES `resources`(`resource_id`)
        ON DELETE CASCADE,
    FOREIGN KEY (`related_resource_id`)
        REFERENCES `resources`(`resource_id`)
        ON DELETE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARACTER SET UTF8MB4;