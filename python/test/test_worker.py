# import unittest

# from api.mutations_queue_workers.Worker import Worker


# class WorkerTestCase(unittest.TestCase):
#     def setUp(self):
#         self.worker = Worker()

#     def test_get_mutation_query(self):
#         query = self.worker.mutations_query(
#             'keyword', 'set', 'bid'
#         )

#         self.assertEqual(type(query), str)

#     def test_process_service_type(self):
#         self.worker.process_service_type(
#             'keyword', 'set', 'bid'
#         )
