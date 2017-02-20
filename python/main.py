#!python2
import math

def get_city_ind(str, names):
    for i,n in names.iteritems():
        if str in n:
            return i
    return -1

def get_names():
    with open("usca312_name.txt") as f:
        data = f.readlines()
        data = filter(lambda x: (len(x) > 0 and x[0] != "#"), data)
        data = dict(zip(range(len(data)), map(lambda x: x.strip(), data)))
    return data

def get_distances(matrix):
    with open("usca312_dist.txt") as f:
        data = f.readlines()
        data = filter(lambda x: (len(x) > 0 and x[0] != "#"), data)
        ds = []
        for line in data:
            ds += line.split()
        dim = int(math.sqrt(len(ds)))
        for i in range(dim):
            matrix.append(ds[i * dim : (i + 1) * dim])

def main():
    names = get_names()
    matrix = []
    get_distances(matrix)

    for i,s in enumerate(matrix):
        matrix[i] = [int((int(x) - 10) / 10) * 11 for x in matrix[i]]
    for i,s in enumerate(matrix):
        matrix[i][i] = 0

    city_from = get_city_ind('New York', names)
    city_to = get_city_ind('Los Angeles', names)

    cities = dict.fromkeys(range(len(names)), float("inf"))
    cities[city_from] = 0
    opt_path = {}

    while cities:
        m = min(cities, key=cities.get)
        if m == city_to:
            break
        for i,x in enumerate(matrix[m]):
            if i in cities and cities[i] > cities[m] + x:
                cities[i] = cities[m] + x
                opt_path[i] = (m, cities[i])
        del cities[m]

    if not city_to in opt_path:
        print 'No path'

    cheap_path = []
    pos = city_to
    while pos != city_from:
        cheap_path.append(names[pos])
        pos = opt_path[pos][0];

    cheap_path.append(names[city_from])
    cheap_path.reverse()

    print cheap_path


if __name__ == '__main__':
    main()
