CREATE TABLE teams ( 
id INT AUTO_INCREMENT PRIMARY KEY, 
class_name VARCHAR(50) NOT NULL UNIQUE, 
points INT DEFAULT 0, 
goals_scored INT DEFAULT 0, 
goals_conceded INT DEFAULT 0 ,
is_visible TINYINT DEFAULT 1
);

CREATE TABLE matches ( 
id INT AUTO_INCREMENT PRIMARY KEY, 
team1_id INT NOT NULL, 
team2_id INT NOT NULL, 
team1_goals INT NOT NULL, 
team2_goals INT NOT NULL, 
match_date DATETIME NOT NULL, 
FOREIGN KEY (team1_id) REFERENCES teams(id), 
FOREIGN KEY (team2_id) REFERENCES teams(id) 

);
