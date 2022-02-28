function MarsRover({data}) {
    return (
        <div>
            <div>
                <span>ID: </span><span>{data.id}</span>
            </div>
            <div>
                <span>Name: </span><span>{data.name}</span>
            </div>
            <div>
                <span>Created at: </span><span>{data.createdAt.date} {data.createdAt.timezone}</span>
            </div>
            <div>
                <span>Coordinates: </span><span>({data.coordinate_x};{data.coordinate_y})</span>
                <span> - </span>
                <span>Orientation: </span><span>({data.orientation}</span>
            </div>
            <div>
                <span>Km: </span><span>{data.km}</span>
            </div>
            <hr />
            <br />
        </div>
    )
}

export default MarsRover