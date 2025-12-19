-- Quick check to see which tables exist
-- Run this in phpMyAdmin SQL tab

SELECT 'users' as table_name, 
       CASE WHEN COUNT(*) > 0 THEN 'EXISTS' ELSE 'MISSING' END as status,
       COUNT(*) as row_count
FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'users'

UNION ALL

SELECT 'organizations' as table_name,
       CASE WHEN COUNT(*) > 0 THEN 'EXISTS' ELSE 'MISSING' END as status,
       COUNT(*) as row_count
FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'organizations'

UNION ALL

SELECT 'goals' as table_name,
       CASE WHEN COUNT(*) > 0 THEN 'EXISTS' ELSE 'MISSING' END as status,
       COUNT(*) as row_count
FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'goals'

UNION ALL

SELECT 'projects' as table_name,
       CASE WHEN COUNT(*) > 0 THEN 'EXISTS' ELSE 'MISSING' END as status,
       COUNT(*) as row_count
FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'projects';


