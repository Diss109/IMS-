-- Query to see latest predictions for all service providers
SELECT
    sp.id AS provider_id,
    sp.name AS provider_name,
    sp.service_type,
    pp.predicted_score,
    pp.confidence_level * 100 AS confidence_percentage,
    pp.prediction_date,
    pp.prediction_period,
    JSON_EXTRACT(pp.factors, '$.trend') AS trend,
    JSON_EXTRACT(pp.factors, '$.slope') AS slope,
    JSON_EXTRACT(pp.factors, '$.r_squared') * 100 AS r_squared_percentage,
    (SELECT COUNT(*) FROM evaluations WHERE service_provider_id = sp.id) AS evaluation_count
FROM
    service_providers sp
LEFT JOIN
    provider_predictions pp ON sp.id = pp.service_provider_id
    AND pp.id = (
        SELECT id FROM provider_predictions
        WHERE service_provider_id = sp.id
        ORDER BY prediction_date DESC
        LIMIT 1
    )
ORDER BY
    pp.predicted_score IS NULL,
    pp.predicted_score ASC;

-- Query to check correlation between predictions and actual scores
SELECT
    sp.id AS provider_id,
    sp.name AS provider_name,
    pp.predicted_score AS previous_prediction,
    e.total_score AS actual_score,
    pp.prediction_date,
    e.created_at AS evaluation_date,
    ABS(pp.predicted_score - e.total_score) AS prediction_error
FROM
    service_providers sp
JOIN
    provider_predictions pp ON sp.id = pp.service_provider_id
JOIN
    evaluations e ON sp.id = e.service_provider_id
WHERE
    e.created_at > pp.prediction_date
ORDER BY
    prediction_error ASC;

-- Query to identify high-risk providers (low scores, high confidence)
SELECT
    sp.id AS provider_id,
    sp.name AS provider_name,
    sp.service_type,
    pp.predicted_score,
    pp.confidence_level * 100 AS confidence_percentage,
    JSON_EXTRACT(pp.factors, '$.trend') AS trend
FROM
    service_providers sp
JOIN
    provider_predictions pp ON sp.id = pp.service_provider_id
    AND pp.id = (
        SELECT id FROM provider_predictions
        WHERE service_provider_id = sp.id
        ORDER BY prediction_date DESC
        LIMIT 1
    )
WHERE
    pp.predicted_score < 60
    AND pp.confidence_level > 0.7
ORDER BY
    pp.predicted_score ASC;
