import {useEffect, useState} from 'react'
import './App.css'
import {GetDashboardData} from "./api";
import MarsRover from "./MarsRover";

function App() {
  const [marsRovers, setMarsRovers] = useState([])

  useEffect(() => {
    GetDashboardData()
        .then(data => setMarsRovers(data))
        .catch(err => console.error(err.message));
  }, [])

  return (
    <div>
      <h3>Mars Rovers</h3>
      {marsRovers.map(marsRover =>
          <MarsRover key={marsRover.id} data={marsRover} />
      )}
    </div>
  )
}

export default App
