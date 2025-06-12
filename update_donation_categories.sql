-- Update donation categories to match the standardized list
UPDATE donations 
SET category = 'Books' 
WHERE category IN ('Fiction', 'Non-Fiction', 'Textbook', 'Children', 'Reference', 'Other', 'books');

UPDATE donations 
SET category = 'Stationery' 
WHERE category IN ('stationery', 'School Supplies');

UPDATE donations 
SET category = 'Uniforms' 
WHERE category IN ('clothing', 'Uniform', 'School Uniform', 'Clothes');

UPDATE donations 
SET category = 'Bags' 
WHERE category IN ('bag', 'School Bag', 'Backpack');

UPDATE donations 
SET category = 'Electronics' 
WHERE category IN ('electronics', 'Electronic', 'Device', 'Gadget');

-- Show the updated categories
SELECT id, title, category, created_at 
FROM donations 
ORDER BY created_at DESC 
LIMIT 10;
