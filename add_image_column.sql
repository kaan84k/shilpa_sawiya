-- Add image column to donations table
ALTER TABLE donations
ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER `condition`;
