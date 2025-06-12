-- Update existing categories to match the new standardized list
-- Map old categories to new ones
UPDATE donations 
SET category = 'Books' 
WHERE category IN ('Fiction', 'Non-Fiction', 'Textbook', 'Academic', 'Children', 'Other', 'book', 'books');

UPDATE donations 
SET category = 'Stationery' 
WHERE category IN ('stationery', 'School Supplies', 'Writing Materials');

UPDATE donations 
SET category = 'Uniforms' 
WHERE category IN ('uniform', 'School Uniform', 'Clothing');

UPDATE donations 
SET category = 'Bags' 
WHERE category IN ('bag', 'School Bag', 'Backpack');

UPDATE donations 
SET category = 'Electronics' 
WHERE category IN ('electronic', 'Device', 'Gadget', 'Computer');

-- Set any remaining categories to 'Other'
UPDATE donations 
SET category = 'Other' 
WHERE category NOT IN ('Books', 'Stationery', 'Uniforms', 'Bags', 'Electronics');
