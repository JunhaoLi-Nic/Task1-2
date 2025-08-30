SELECT
  u.Username,
  MAX(CASE WHEN ufn.Field = 'Phone' THEN ud.Data END) AS Phone,
  MAX(CASE WHEN ufn.Field = 'Email' THEN ud.Data END) AS Email,
  MAX(CASE WHEN ufn.Field = 'Position' THEN ud.Data END) AS Position
FROM
  User u
LEFT JOIN UserData ud ON u.ID = ud.UserID
LEFT JOIN UserFieldName ufn ON ud.FieldID = ufn.ID
GROUP BY
  u.ID, u.Username
ORDER BY
  u.ID;